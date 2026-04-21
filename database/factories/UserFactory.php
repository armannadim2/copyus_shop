<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => Hash::make('password'),
            'company_name'      => fake()->company(),
            'cif'               => 'B' . fake()->numerify('########'),
            'phone'             => fake()->phoneNumber(),
            'address'           => fake()->streetAddress(),
            'city'              => fake()->city(),
            'postal_code'       => fake()->postcode(),
            'country'           => 'ES',
            'role'              => 'pending',
            'requires_invoice'  => false,
            'locale'            => 'ca',
            'is_active'         => true,
            'approved_at'       => null,
            'email_verified_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role'        => 'admin',
            'approved_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'role'        => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'role'        => 'pending',
            'approved_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'role'        => 'rejected',
            'approved_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
