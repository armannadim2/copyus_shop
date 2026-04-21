<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoCodeTest extends TestCase
{
    use RefreshDatabase;

    private function makeActiveCode(array $overrides = []): PromoCode
    {
        return PromoCode::create(array_merge([
            'code'      => 'SAVE10',
            'type'      => 'percent',
            'value'     => 10.00,
            'is_active' => true,
        ], $overrides));
    }

    private function buildCart(User $user, float $price = 100.00): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'price'     => $price,
            'stock'     => 50,
        ]);

        CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'type'       => 'cart',
        ]);
    }

    // -------------------------------------------------------
    // Apply Promo Code
    // -------------------------------------------------------

        public function test_approved_user_can_apply_a_valid_promo_code(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user, 100.00);
        $this->makeActiveCode(['code' => 'SAVE10']);

        $this->actingAs($user)
             ->post(route('promo.apply'), ['code' => 'SAVE10'])
             ->assertRedirect();

        $this->assertEquals('SAVE10', session('promo_code'));
    }

        public function test_applying_invalid_code_shows_error(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('promo.apply'), ['code' => 'DOESNOTEXIST'])
             ->assertRedirect()
             ->assertSessionHas('promo_error');
    }

        public function test_applying_inactive_code_shows_error(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user, 100.00);
        $this->makeActiveCode(['code' => 'INACTIVE', 'is_active' => false]);

        $this->actingAs($user)
             ->post(route('promo.apply'), ['code' => 'INACTIVE'])
             ->assertRedirect()
             ->assertSessionHas('promo_error');
    }

        public function test_applying_code_below_minimum_order_shows_error(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user, 30.00);
        $this->makeActiveCode(['code' => 'MIN100', 'min_order_total' => 100.00]);

        $this->actingAs($user)
             ->post(route('promo.apply'), ['code' => 'MIN100'])
             ->assertRedirect()
             ->assertSessionHas('promo_error');
    }

        public function test_apply_requires_code_field(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('promo.apply'), [])
             ->assertSessionHasErrors('code');
    }

    // -------------------------------------------------------
    // Remove Promo Code
    // -------------------------------------------------------

        public function test_approved_user_can_remove_applied_promo_code(): void
    {
        $user = User::factory()->approved()->create();
        session(['promo_code' => 'SAVE10']);

        $this->actingAs($user)
             ->post(route('promo.remove'))
             ->assertRedirect();

        $this->assertNull(session('promo_code'));
    }

        public function test_remove_promo_code_shows_success_flash(): void
    {
        $user = User::factory()->approved()->create();
        session(['promo_code' => 'SAVE10']);

        $this->actingAs($user)
             ->post(route('promo.remove'))
             ->assertSessionHas('promo_success');
    }

    // -------------------------------------------------------
    // Promo applied at checkout increments used_count
    // -------------------------------------------------------

        public function test_placing_order_with_promo_code_increments_used_count(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user, 100.00);
        $code = $this->makeActiveCode(['code' => 'SAVE10', 'used_count' => 0]);
        session(['promo_code' => 'SAVE10']);

        $this->actingAs($user)
             ->post(route('orders.place'), [
                 'shipping_address' => 'Carrer Test 1',
                 'shipping_city'    => 'Barcelona',
                 'shipping_postal'  => '08001',
                 'shipping_country' => 'ES',
             ]);

        $this->assertEquals(1, $code->fresh()->used_count);
    }
}
