<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return [
            'category_id'        => Category::factory(),
            'sku'                => strtoupper(fake()->unique()->bothify('??-####')),
            'slug'               => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'brand'              => fake()->company(),
            'name'               => ['ca' => $name, 'es' => $name, 'en' => $name],
            'short_description'  => ['ca' => fake()->sentence(), 'es' => fake()->sentence(), 'en' => fake()->sentence()],
            'description'        => ['ca' => fake()->paragraph(), 'es' => fake()->paragraph(), 'en' => fake()->paragraph()],
            'price'              => fake()->randomFloat(4, 1, 500),
            'vat_rate'           => 21.00,
            'stock'              => fake()->numberBetween(0, 200),
            'min_order_quantity' => 1,
            'unit'               => 'unit',
            'is_active'          => true,
            'is_featured'        => false,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn() => ['is_featured' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn() => ['stock' => 0]);
    }

    public function withPrice(float $price): static
    {
        return $this->state(fn() => ['price' => $price]);
    }
}
