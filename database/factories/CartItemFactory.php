<?php

namespace Database\Factories;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory()->approved(),
            'product_id' => Product::factory(),
            'quantity'   => fake()->numberBetween(1, 10),
            'type'       => 'cart',
        ];
    }

    public function quote(): static
    {
        return $this->state(fn() => ['type' => 'quote']);
    }
}
