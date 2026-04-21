<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
        public function test_it_identifies_admin_role(): void
    {
        $user = User::factory()->admin()->make();
        $this->assertTrue($user->is_admin);
        $this->assertFalse($user->is_approved);
    }

        public function test_it_identifies_approved_b2b_role(): void
    {
        $user = User::factory()->approved()->make();
        $this->assertTrue($user->is_approved);
        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->is_pending);
    }

        public function test_it_identifies_pending_role(): void
    {
        $user = User::factory()->pending()->make();
        $this->assertTrue($user->is_pending);
        $this->assertFalse($user->is_approved);
    }

        public function test_it_identifies_rejected_role(): void
    {
        $user = User::factory()->rejected()->make();
        $this->assertTrue($user->is_rejected);
        $this->assertFalse($user->is_pending);
    }

        public function test_it_builds_full_address(): void
    {
        $user = User::factory()->make([
            'address'     => 'Carrer Major 10',
            'city'        => 'Barcelona',
            'postal_code' => '08001',
            'country'     => 'ES',
        ]);

        $this->assertStringContainsString('Barcelona', $user->full_address);
        $this->assertStringContainsString('08001', $user->full_address);
    }

        public function test_it_hides_password_in_serialization(): void
    {
        $user = User::factory()->make();
        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
    }

        public function test_it_has_correct_relationships_defined(): void
    {
        $user = new User();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
            $user->cart()
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $user->orders()
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $user->quotations()
        );
    }
}
