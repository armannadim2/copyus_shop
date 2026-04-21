<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancelTest extends TestCase
{
    use RefreshDatabase;

    private function buildCart(User $user): void
    {
        $product = Product::factory()->create(['is_active' => true, 'stock' => 50, 'price' => 50.00]);

        CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1,
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
    // Cancel Order
    // -------------------------------------------------------

        public function test_user_can_cancel_a_pending_order(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);

        $this->actingAs($user)
             ->post(route('orders.cancel', $order->order_number))
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'cancelled',
        ]);
    }

        public function test_user_can_cancel_a_confirmed_order(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => 'confirmed',
        ]);

        $this->actingAs($user)
             ->post(route('orders.cancel', $order->order_number))
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'cancelled',
        ]);
    }

        public function test_user_cannot_cancel_a_shipped_order(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => 'shipped',
        ]);

        $this->actingAs($user)
             ->post(route('orders.cancel', $order->order_number))
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'shipped',
        ]);
    }

        public function test_user_cannot_cancel_another_users_order(): void
    {
        $user1 = User::factory()->approved()->create();
        $user2 = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id' => $user2->id,
            'status'  => 'pending',
        ]);

        $this->actingAs($user1)
             ->post(route('orders.cancel', $order->order_number))
             ->assertNotFound();

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'pending',
        ]);
    }

    // -------------------------------------------------------
    // Payment Reference
    // -------------------------------------------------------

        public function test_user_can_submit_payment_reference_for_unpaid_order(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id'        => $user->id,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($user)
             ->post(route('orders.payment-reference', $order->order_number), [
                 'payment_reference' => 'TRANSFER-20260418-001',
             ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'                => $order->id,
            'payment_reference' => 'TRANSFER-20260418-001',
        ]);
    }

        public function test_payment_reference_requires_non_empty_value(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id'        => $user->id,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($user)
             ->post(route('orders.payment-reference', $order->order_number), [
                 'payment_reference' => '',
             ])->assertSessionHasErrors('payment_reference');
    }

        public function test_user_cannot_submit_payment_reference_for_another_users_order(): void
    {
        $user1 = User::factory()->approved()->create();
        $user2 = User::factory()->approved()->create();
        $order = Order::factory()->create([
            'user_id'        => $user2->id,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($user1)
             ->post(route('orders.payment-reference', $order->order_number), [
                 'payment_reference' => 'REF-001',
             ])->assertNotFound();
    }

    // -------------------------------------------------------
    // Reorder
    // -------------------------------------------------------

        public function test_user_can_reorder_a_delivered_order(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true, 'stock' => 50, 'price' => 30.00]);
        $order   = Order::factory()->for($user)->delivered()->create();

        \App\Models\OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $this->actingAs($user)
             ->post(route('orders.reorder', $order->order_number))
             ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'type'       => 'cart',
        ]);
    }
}
