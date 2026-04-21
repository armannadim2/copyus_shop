<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationBasketTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Quote Basket Access
    // -------------------------------------------------------

        public function test_guest_cannot_view_quote_basket(): void
    {
        $this->get(route('quotations.basket'))
             ->assertRedirect(route('login'));
    }

        public function test_approved_user_can_view_quote_basket(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('quotations.basket'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Add to Quote Basket
    // -------------------------------------------------------

        public function test_approved_user_can_add_product_to_quote_basket(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('quotations.add'), [
                 'product_id' => $product->id,
                 'quantity'   => 5,
             ])->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'type'       => 'quote',
            'quantity'   => 5,
        ]);
    }

        public function test_add_to_quote_basket_requires_valid_product(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('quotations.add'), [
                 'product_id' => 99999,
                 'quantity'   => 1,
             ])->assertSessionHasErrors('product_id');
    }

    // -------------------------------------------------------
    // Update Quote Basket Item
    // -------------------------------------------------------

        public function test_approved_user_can_update_quote_basket_item(): void
    {
        $user = User::factory()->approved()->create();
        $item = CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 3,
            'type'       => 'quote',
        ]);

        $this->actingAs($user)
             ->patch(route('quotations.update', $item->id), ['quantity' => 10])
             ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'id'       => $item->id,
            'quantity' => 10,
        ]);
    }

    // -------------------------------------------------------
    // Remove from Quote Basket
    // -------------------------------------------------------

        public function test_approved_user_can_remove_item_from_quote_basket(): void
    {
        $user = User::factory()->approved()->create();
        $item = CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 2,
            'type'       => 'quote',
        ]);

        $this->actingAs($user)
             ->delete(route('quotations.remove', $item->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    // -------------------------------------------------------
    // Accept Quotation → creates Order
    // -------------------------------------------------------

        public function test_accepting_a_quoted_quotation_creates_an_order(): void
    {
        $user      = User::factory()->approved()->create();
        $product   = Product::factory()->create(['is_active' => true, 'stock' => 20]);
        $quotation = Quotation::factory()->quoted()->create(['user_id' => $user->id]);

        QuotationItem::factory()->create([
            'quotation_id' => $quotation->id,
            'product_id'   => $product->id,
            'quantity'     => 2,
            'quoted_price' => 24.20,
            'vat_rate'     => 21.00,
        ]);

        $this->actingAs($user)
             ->patch(route('quotations.accept', $quotation->quote_number))
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status'  => 'confirmed',
        ]);

        $this->assertDatabaseHas('quotations', [
            'id'     => $quotation->id,
            'status' => 'accepted',
        ]);
    }
}
