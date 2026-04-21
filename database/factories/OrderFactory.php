<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal   = fake()->randomFloat(4, 10, 1000);
        $vatAmount  = round($subtotal * 0.21, 4);
        $total      = round($subtotal + $vatAmount, 4);

        return [
            'user_id'          => User::factory()->approved(),
            'order_number'     => 'ORD-' . strtoupper(fake()->unique()->bothify('######')),
            'status'           => 'pending',
            'subtotal'         => $subtotal,
            'vat_amount'       => $vatAmount,
            'total'            => $total,
            'shipping_address' => [
                'name'    => fake()->name(),
                'address' => fake()->streetAddress(),
                'city'    => fake()->city(),
                'zip'     => fake()->postcode(),
                'country' => 'ES',
            ],
            'billing_address'  => [
                'name'    => fake()->name(),
                'address' => fake()->streetAddress(),
                'city'    => fake()->city(),
                'zip'     => fake()->postcode(),
                'country' => 'ES',
            ],
            'placed_at'        => now(),
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn() => ['status' => $status]);
    }

    public function cancelled(): static
    {
        return $this->state(fn() => ['status' => 'cancelled']);
    }

    public function delivered(): static
    {
        return $this->state(fn() => ['status' => 'delivered']);
    }
}
