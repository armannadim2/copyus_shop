<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal  = fake()->randomFloat(4, 10, 1000);
        $vat       = round($subtotal * 0.21, 4);
        $total     = round($subtotal + $vat, 4);

        return [
            'order_id'        => Order::factory(),
            'user_id'         => User::factory(),
            'invoice_number'  => 'INV-' . strtoupper(fake()->unique()->bothify('######')),
            'status'          => 'issued',
            'subtotal'        => $subtotal,
            'vat_amount'      => $vat,
            'total'           => $total,
            'locale'          => 'ca',
            'company_details' => [
                'name'    => 'Copyus SL',
                'cif'     => 'B12345678',
                'address' => 'Carrer Test 1',
            ],
            'billing_address' => [
                'name'    => fake()->name(),
                'address' => fake()->streetAddress(),
            ],
            'issued_at'       => now(),
            'due_at'          => now()->addDays(30),
            'paid_at'         => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn() => [
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn() => [
            'status' => 'overdue',
            'due_at' => now()->subDays(5),
        ]);
    }
}
