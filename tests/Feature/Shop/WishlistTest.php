<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_view_wishlist(): void
    {
        $this->get(route('wishlist.index'))
             ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_view_wishlist(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
             ->get(route('wishlist.index'))
             ->assertForbidden();
    }

        public function test_approved_user_can_view_wishlist(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('wishlist.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Toggle
    // -------------------------------------------------------

        public function test_approved_user_can_add_product_to_wishlist(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('wishlist.toggle', $product->id))
             ->assertRedirect();

        $this->assertDatabaseHas('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

        public function test_toggling_again_removes_product_from_wishlist(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->actingAs($user)
             ->post(route('wishlist.toggle', $product->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    // -------------------------------------------------------
    // Remove
    // -------------------------------------------------------

        public function test_approved_user_can_remove_product_from_wishlist(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->actingAs($user)
             ->delete(route('wishlist.destroy', $product->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('wishlists', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
