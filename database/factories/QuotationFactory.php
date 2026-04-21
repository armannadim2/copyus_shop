<?php

namespace Database\Factories;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        return [
            'user_id'         => User::factory()->approved(),
            'quote_number'    => 'QUO-' . strtoupper(fake()->unique()->bothify('######')),
            'status'          => 'pending',
            'quoted_subtotal' => null,
            'quoted_vat'      => null,
            'quoted_total'    => null,
            'admin_notes'     => null,
            'customer_notes'  => fake()->sentence(),
            'valid_until'     => null,
        ];
    }

    public function quoted(): static
    {
        $subtotal = fake()->randomFloat(4, 50, 1000);
        $vat      = round($subtotal * 0.21, 4);
        $total    = round($subtotal + $vat, 4);

        return $this->state(fn() => [
            'status'          => 'quoted',
            'quoted_subtotal' => $subtotal,
            'quoted_vat'      => $vat,
            'quoted_total'    => $total,
            'valid_until'     => now()->addDays(30),
        ]);
    }

    public function accepted(): static
    {
        return $this->quoted()->state(fn() => [
            'status' => 'accepted',
        ]);
    }

    public function expired(): static
    {
        return $this->quoted()->state(fn() => [
            'valid_until' => now()->subDay(),
        ]);
    }
}
