<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function buildCart(User $user, int $qty = 2): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'stock'     => 50,
            'price'     => 100.00,
        ]);

        CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => $qty,
            'type'       => 'cart',
        ]);
    }

    private function placePayload(): array
    {
        return [
            'shipping_address' => 'Carrer Test 1',
            'shipping_city'    => 'Barcelona',
            'shipping_postal'  => '08001',
            'shipping_country' => 'ES',
        ];
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_orders(): void
    {
        $this->get(route('orders.index'))
            ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_access_orders(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertForbidden();
    }

        public function test_approved_user_can_view_orders(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertViewIs('shop.orders.index');
    }

    // -------------------------------------------------------
    // Checkout
    // -------------------------------------------------------

        public function test_approved_user_can_view_checkout_with_cart_items(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user);

        $this->actingAs($user)
            ->get(route('orders.checkout'))
            ->assertOk()
            ->assertViewIs('shop.orders.checkout');
    }

        public function test_checkout_redirects_if_cart_is_empty(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('orders.checkout'))
            ->assertRedirect(route('cart.index'));
    }

    // -------------------------------------------------------
    // Place Order
    // -------------------------------------------------------

        public function test_approved_user_can_place_order(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user);

        $this->actingAs($user)
            ->post(route('orders.place'), $this->placePayload())
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);
    }

        public function test_placing_order_clears_the_cart(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user);

        $this->actingAs($user)
            ->post(route('orders.place'), $this->placePayload());

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'type'    => 'cart',
        ]);
    }

        public function test_order_number_is_generated_on_place(): void
    {
        $user = User::factory()->approved()->create();
        $this->buildCart($user);

        $this->actingAs($user)
            ->post(route('orders.place'), $this->placePayload());

        $order = Order::where('user_id', $user->id)->first();

        $this->assertNotNull($order);
        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    // -------------------------------------------------------
    // Order Detail
    // -------------------------------------------------------

        public function test_user_can_view_their_own_order(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('orders.show', $order->order_number))
            ->assertOk()
            ->assertViewIs('shop.orders.show');
    }

        public function test_user_cannot_view_another_users_order(): void
    {
        $user1 = User::factory()->approved()->create();
        $user2 = User::factory()->approved()->create();
        $order = Order::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user1)
            ->get(route('orders.show', $order->order_number))
            ->assertNotFound();
    }
}
