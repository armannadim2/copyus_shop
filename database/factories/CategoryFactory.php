<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();
        return [
            'name'        => ['ca' => $name, 'es' => $name, 'en' => $name],
            'description' => ['ca' => fake()->sentence(), 'es' => fake()->sentence(), 'en' => fake()->sentence()],
            'slug'        => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'sort_order'  => fake()->numberBetween(1, 100),
            'is_active'   => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
