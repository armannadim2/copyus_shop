<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoCodeModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeCode(array $overrides = []): PromoCode
    {
        return PromoCode::create(array_merge([
            'code'      => 'TEST' . uniqid(),
            'type'      => 'percent',
            'value'     => 10.00,
            'is_active' => true,
        ], $overrides));
    }

    // -------------------------------------------------------
    // isValid — inactive / date gates
    // -------------------------------------------------------

        public function test_inactive_code_is_not_valid(): void
    {
        $code = $this->makeCode(['is_active' => false]);
        $this->assertNotTrue($code->isValid(100));
    }

        public function test_code_not_yet_valid_is_rejected(): void
    {
        $code = $this->makeCode(['valid_from' => now()->addDay()]);
        $this->assertNotTrue($code->isValid(100));
    }

        public function test_expired_code_is_rejected(): void
    {
        $code = $this->makeCode(['valid_until' => now()->subDay()]);
        $this->assertNotTrue($code->isValid(100));
    }

        public function test_code_with_valid_dates_is_accepted(): void
    {
        $code = $this->makeCode([
            'valid_from'  => now()->subDay(),
            'valid_until' => now()->addDay(),
        ]);
        $this->assertTrue($code->isValid(100));
    }

    // -------------------------------------------------------
    // isValid — usage limits
    // -------------------------------------------------------

        public function test_code_exhausted_max_uses_is_rejected(): void
    {
        $code = $this->makeCode(['max_uses' => 5, 'used_count' => 5]);
        $this->assertNotTrue($code->isValid(100));
    }

        public function test_code_below_max_uses_is_accepted(): void
    {
        $code = $this->makeCode(['max_uses' => 5, 'used_count' => 4]);
        $this->assertTrue($code->isValid(100));
    }

    // -------------------------------------------------------
    // isValid — minimum order total
    // -------------------------------------------------------

        public function test_code_below_minimum_order_total_is_rejected(): void
    {
        $code = $this->makeCode(['min_order_total' => 100.00]);
        $this->assertNotTrue($code->isValid(50.00));
    }

        public function test_code_at_or_above_minimum_order_total_is_accepted(): void
    {
        $code = $this->makeCode(['min_order_total' => 100.00]);
        $this->assertTrue($code->isValid(100.00));
    }

    // -------------------------------------------------------
    // isValid — per-user limit
    // -------------------------------------------------------

        public function test_code_exceeding_per_user_limit_is_rejected(): void
    {
        $user = User::factory()->approved()->create();
        $code = $this->makeCode(['max_uses_per_user' => 1]);

        Order::factory()->create([
            'user_id'    => $user->id,
            'promo_code' => $code->code,
        ]);

        $this->assertNotTrue($code->isValid(100, $user));
    }

        public function test_code_within_per_user_limit_is_accepted(): void
    {
        $user = User::factory()->approved()->create();
        $code = $this->makeCode(['max_uses_per_user' => 2]);

        Order::factory()->create([
            'user_id'    => $user->id,
            'promo_code' => $code->code,
        ]);

        $this->assertTrue($code->isValid(100, $user));
    }

    // -------------------------------------------------------
    // calculateDiscount
    // -------------------------------------------------------

        public function test_it_calculates_percent_discount(): void
    {
        $code = $this->makeCode(['type' => 'percent', 'value' => 20.00]);
        $this->assertEquals(20.00, $code->calculateDiscount(100.00));
    }

        public function test_it_calculates_fixed_discount(): void
    {
        $code = $this->makeCode(['type' => 'fixed', 'value' => 15.00]);
        $this->assertEquals(15.00, $code->calculateDiscount(100.00));
    }

        public function test_fixed_discount_is_capped_at_subtotal(): void
    {
        $code = $this->makeCode(['type' => 'fixed', 'value' => 200.00]);
        $this->assertEquals(50.00, $code->calculateDiscount(50.00));
    }

        public function test_percent_discount_applies_correctly_to_fractional_subtotal(): void
    {
        $code = $this->makeCode(['type' => 'percent', 'value' => 10.00]);
        $this->assertEquals(1.5, $code->calculateDiscount(15.00));
    }
}
