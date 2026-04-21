<?php

namespace App\Services;

use App\Models\PrintQuantityTier;
use App\Models\PrintTemplate;
use App\Models\User;

class PrintPriceCalculator
{
    /**
     * Calculate the price and production time for a print job configuration.
     *
     * The $template must have its `options` relationship loaded (with nested `values`).
     *
     * @param  PrintTemplate        $template
     * @param  array<string,string> $configuration   option_key => value_key
     * @param  int                  $quantity
     * @param  User|null            $user            reserved for future user-specific pricing
     */
    public function calculate(
        PrintTemplate $template,
        array $configuration,
        int $quantity,
        ?User $user = null
    ): PrintPriceResult {
        $locale = app()->getLocale();

        // ── 1. Base unit price ────────────────────────────────────────────────
        $baseUnitPrice = (float) $template->base_price;
        $breakdown     = [[
            'label' => __('print.base_price', [], $locale) ?: 'Preu base',
            'delta' => $baseUnitPrice,
        ]];

        // ── 2. Option modifiers ───────────────────────────────────────────────
        $optionDelta  = 0.0;
        $productionDays = (int) $template->base_production_days;

        // Build a lookup map: option_key => [value_key => PrintOptionValue]
        $optionMap = [];
        foreach ($template->options as $option) {
            foreach ($option->values as $value) {
                $optionMap[$option->key][$value->value_key] = $value;
            }
        }

        foreach ($configuration as $optionKey => $valueKey) {
            $value = $optionMap[$optionKey][$valueKey] ?? null;
            if (!$value) continue;

            $modifier = (float) $value->price_modifier;

            if ($modifier == 0.0) {
                // Still track production days
                $productionDays += (int) $value->production_days_modifier;
                continue;
            }

            if ($value->price_modifier_type === 'percent') {
                $delta = $baseUnitPrice * ($modifier / 100);
            } else {
                $delta = $modifier;
            }

            $optionDelta  += $delta;
            $productionDays += (int) $value->production_days_modifier;

            // Omit zero-delta items from the breakdown for cleanliness
            $label = $value->getTranslation('label', $locale);
            $breakdown[] = ['label' => $label, 'delta' => round($delta, 4)];
        }

        $unitPriceBeforeTier = $baseUnitPrice + $optionDelta;

        // ── 3. Quantity tier discount ─────────────────────────────────────────
        $tier                = PrintQuantityTier::resolve($template, $quantity);
        $tierDiscountPercent = $tier ? (float) $tier->discount_percent : 0.0;
        $unitPrice           = $unitPriceBeforeTier * (1 - $tierDiscountPercent / 100);

        if ($tierDiscountPercent > 0) {
            $breakdown[] = [
                'label' => sprintf('Descompte per volum (-%s%%)', number_format($tierDiscountPercent, 0)),
                'delta' => round($unitPrice - $unitPriceBeforeTier, 4),
            ];
        }

        $totalPrice     = $unitPrice * $quantity;
        $productionDays = max(1, $productionDays);

        return new PrintPriceResult(
            unitPrice:           round($unitPrice, 4),
            totalPrice:          round($totalPrice, 4),
            tierDiscountPercent: $tierDiscountPercent,
            productionDays:      $productionDays,
            breakdown:           $breakdown,
        );
    }

    /**
     * Validate that a configuration is complete and all value_keys are valid
     * for the given template's required options.
     *
     * Returns an array of validation error messages (empty = valid).
     */
    public function validate(PrintTemplate $template, array $configuration): array
    {
        $errors = [];

        foreach ($template->options as $option) {
            if (!$option->is_required) continue;

            $valueKey = $configuration[$option->key] ?? null;

            if (!$valueKey) {
                $errors[] = sprintf(
                    'L\'opció "%s" és obligatòria.',
                    $option->getTranslation('label', app()->getLocale())
                );
                continue;
            }

            $validKeys = $option->values->pluck('value_key')->all();
            if (!in_array($valueKey, $validKeys, true)) {
                $errors[] = sprintf(
                    'Valor "%s" invàlid per a l\'opció "%s".',
                    $valueKey,
                    $option->getTranslation('label', app()->getLocale())
                );
            }
        }

        return $errors;
    }
}
