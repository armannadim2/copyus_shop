<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates Spanish fiscal identifiers (CIF, NIF/DNI, NIE) and EU VAT numbers.
 *
 * Accepted formats:
 *  - Spanish DNI  : 8 digits + control letter        e.g. 12345678Z
 *  - Spanish NIE  : X/Y/Z + 7 digits + control letter  e.g. X1234567L
 *  - Spanish CIF  : letter + 7 digits + control char  e.g. B12345678
 *  - EU VAT       : 2-letter ISO country code + country-specific number
 */
class FiscalIdentity implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = strtoupper(trim(str_replace([' ', '-', '.'], '', $value)));

        if ($this->isValidDni($value)) {
            return;
        }

        if ($this->isValidNie($value)) {
            return;
        }

        if ($this->isValidCif($value)) {
            return;
        }

        if ($this->isValidEuVat($value)) {
            return;
        }

        $fail(__('validation.fiscal_identity'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Spanish DNI  (8 digits + control letter)
    // ──────────────────────────────────────────────────────────────────────────
    private function isValidDni(string $value): bool
    {
        if (!preg_match('/^\d{8}[A-Z]$/', $value)) {
            return false;
        }

        return $value[8] === $this->dniLetter((int) substr($value, 0, 8));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Spanish NIE  (X|Y|Z + 7 digits + control letter)
    // ──────────────────────────────────────────────────────────────────────────
    private function isValidNie(string $value): bool
    {
        if (!preg_match('/^[XYZ]\d{7}[A-Z]$/', $value)) {
            return false;
        }

        $map    = ['X' => '0', 'Y' => '1', 'Z' => '2'];
        $number = $map[$value[0]] . substr($value, 1, 7);

        return $value[8] === $this->dniLetter((int) $number);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Spanish CIF  (letter + 7 digits + control digit-or-letter)
    //
    // First letter codes:
    //   A-H, J  → corporations / partnerships
    //   K, L, M → special cases (non-resident individuals, foreigners, foreign entities)
    //   N       → non-resident entities
    //   P, Q    → public bodies / agencies
    //   R       → religious orders
    //   S       → state administration bodies
    //   U       → temporary joint ventures (UTE)
    //   V       → other types
    //   W       → foreign permanent establishments
    // ──────────────────────────────────────────────────────────────────────────
    private function isValidCif(string $value): bool
    {
        if (!preg_match('/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/', $value)) {
            return false;
        }

        $digits      = substr($value, 1, 7);
        $control     = $value[8];
        $firstLetter = $value[0];

        $sumOdd  = 0;
        $sumEven = 0;

        for ($i = 0; $i < 7; $i++) {
            $d = (int) $digits[$i];

            if ($i % 2 === 0) {            // positions 1, 3, 5, 7 (1-based) → double
                $d *= 2;
                if ($d > 9) {
                    $d -= 9;
                }
                $sumOdd += $d;
            } else {                        // positions 2, 4, 6 (1-based) → plain sum
                $sumEven += $d;
            }
        }

        $total        = $sumOdd + $sumEven;
        $controlDigit = (10 - ($total % 10)) % 10;
        $controlLetter = 'JABCDEFGHI'[$controlDigit];

        // Types that always use a letter control character
        if (in_array($firstLetter, ['P', 'Q', 'S', 'W', 'R'])) {
            return $control === $controlLetter;
        }

        // Types that always use a digit control character
        if (in_array($firstLetter, ['A', 'B', 'E', 'H'])) {
            return $control === (string) $controlDigit;
        }

        // All others accept either
        return $control === (string) $controlDigit || $control === $controlLetter;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // EU VAT numbers  (structural/format validation)
    //
    // Reference: https://ec.europa.eu/taxation_customs/vies/
    // ──────────────────────────────────────────────────────────────────────────
    private function isValidEuVat(string $value): bool
    {
        // Must start with 2-letter EU country prefix
        if (!preg_match('/^([A-Z]{2})(.+)$/', $value, $m)) {
            return false;
        }

        [$country, $number] = [$m[1], $m[2]];

        $patterns = [
            'AT' => '/^U\d{8}$/',                              // ATU12345678
            'BE' => '/^0?\d{9,10}$/',                          // BE0123456789
            'BG' => '/^\d{9,10}$/',                            // BG123456789
            'CY' => '/^\d{8}[A-Z]$/',                          // CY12345678A
            'CZ' => '/^\d{8,10}$/',                            // CZ12345678
            'DE' => '/^\d{9}$/',                               // DE123456789
            'DK' => '/^\d{8}$/',                               // DK12345678
            'EE' => '/^\d{9}$/',                               // EE123456789
            'ES' => '/^[A-Z0-9]\d{7}[A-Z0-9]$/',              // Caught by CIF/DNI/NIE above
            'FI' => '/^\d{8}$/',                               // FI12345678
            'FR' => '/^[A-HJ-NP-Z0-9]{2}\d{9}$/',             // FR12345678901
            'GB' => '/^(\d{9}|\d{12}|GD\d{3}|HA\d{3})$/',     // GB123456789
            'GR' => '/^\d{9}$/',                               // EL (Greece uses EL prefix)
            'EL' => '/^\d{9}$/',                               // EL123456789
            'HR' => '/^\d{11}$/',                              // HR12345678901
            'HU' => '/^\d{8}$/',                               // HU12345678
            'IE' => '/^\d{7}[A-W]([A-I])?$/',                  // IE1234567A
            'IT' => '/^\d{11}$/',                              // IT12345678901
            'LT' => '/^(\d{9}|\d{12})$/',                      // LT123456789
            'LU' => '/^\d{8}$/',                               // LU12345678
            'LV' => '/^\d{11}$/',                              // LV12345678901
            'MT' => '/^\d{8}$/',                               // MT12345678
            'NL' => '/^\d{9}B\d{2}$/',                         // NL123456789B01
            'PL' => '/^\d{10}$/',                              // PL1234567890
            'PT' => '/^\d{9}$/',                               // PT123456789
            'RO' => '/^\d{2,10}$/',                            // RO12
            'SE' => '/^\d{12}$/',                              // SE123456789012
            'SI' => '/^\d{8}$/',                               // SI12345678
            'SK' => '/^\d{10}$/',                              // SK1234567890
        ];

        if (!isset($patterns[$country])) {
            return false;
        }

        return (bool) preg_match($patterns[$country], $number);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────
    private function dniLetter(int $number): string
    {
        return 'TRWAGMYFPDXBNJZSQVHLCKE'[$number % 23];
    }
}
