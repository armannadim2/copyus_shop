<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_cart(): void
    {
        $this->get(route('cart.index'))
             ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_access_cart(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
             ->get(route('cart.index'))
             ->assertForbidden();
    }

        public function test_approved_user_can_view_cart(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('cart.index'))
             ->assertOk()
             ->assertViewIs('shop.cart.index');
    }

    // -------------------------------------------------------
    // Add to Cart
    // -------------------------------------------------------

        public function test_approved_user_can_add_product_to_cart(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create([
            'is_active' => true,
            'stock'     => 20,
        ]);

        $this->actingAs($user)
             ->post(route('cart.add'), [
                 'product_id' => $product->id,
                 'quantity'   => 2,
             ])->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'type'       => 'cart',
        ]);
    }

        public function test_adding_same_product_twice_increments_quantity(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create([
            'is_active' => true,
            'stock'     => 50,
        ]);

        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 3,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 5,
            'type'       => 'cart',
        ]);
    }

    // -------------------------------------------------------
    // Update Cart
    // -------------------------------------------------------

        public function test_approved_user_can_update_cart_item_quantity(): void
    {
        $user = User::factory()->approved()->create();
        $item = CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 2,
            'type'       => 'cart',
        ]);

        $this->actingAs($user)
             ->patch(route('cart.update', $item->id), ['quantity' => 5])
             ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'id'       => $item->id,
            'quantity' => 5,
        ]);
    }

        public function test_user_cannot_update_another_users_cart_item(): void
    {
        $user1 = User::factory()->approved()->create();
        $user2 = User::factory()->approved()->create();
        $item  = CartItem::factory()->create([
            'user_id'    => $user2->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 2,
            'type'       => 'cart',
        ]);

        $this->actingAs($user1)
             ->patch(route('cart.update', $item->id), ['quantity' => 5])
             ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'id'       => $item->id,
            'quantity' => 2,
        ]);
    }

    // -------------------------------------------------------
    // Remove from Cart
    // -------------------------------------------------------

        public function test_approved_user_can_remove_item_from_cart(): void
    {
        $user = User::factory()->approved()->create();
        $item = CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 1,
            'type'       => 'cart',
        ]);

        $this->actingAs($user)
             ->delete(route('cart.remove', $item->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

        public function test_approved_user_can_clear_entire_cart(): void
    {
        $user = User::factory()->approved()->create();

        CartItem::factory()->count(3)->create([
            'user_id' => $user->id,
            'type'    => 'cart',
        ]);

        $this->actingAs($user)
             ->delete(route('cart.clear'))
             ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'type'    => 'cart',
        ]);
    }
}
