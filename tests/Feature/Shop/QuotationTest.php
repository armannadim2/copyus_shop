<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_quotations(): void
    {
        $this->get(route('quotations.index'))
            ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_access_quotations(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('quotations.index'))
            ->assertForbidden();
    }

        public function test_approved_user_can_view_quotations(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('quotations.index'))
            ->assertOk()
            ->assertViewIs('shop.quotations.index');
    }

    // -------------------------------------------------------
    // Submit Quotation (from basket)
    // -------------------------------------------------------

        public function test_approved_user_can_submit_quotation_from_basket(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 10,
            'type'       => 'quote',
        ]);

        $this->actingAs($user)
            ->post(route('quotations.submit'), [
                'customer_notes' => 'Please quote ASAP.',
            ])->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'product_id' => $product->id,
            'quantity'   => 10,
        ]);
    }

        public function test_submit_redirects_to_basket_when_basket_is_empty(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->post(route('quotations.submit'))
            ->assertRedirect(route('quotations.basket'));
    }

        public function test_quotation_number_is_generated_on_submit(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        CartItem::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 5,
            'type'       => 'quote',
        ]);

        $this->actingAs($user)
            ->post(route('quotations.submit'));

        $quotation = Quotation::where('user_id', $user->id)->first();

        $this->assertNotNull($quotation->quote_number);
        $this->assertStringStartsWith('QUO-', $quotation->quote_number);
    }

    // -------------------------------------------------------
    // View Quotation
    // -------------------------------------------------------

        public function test_user_can_view_their_own_quotation(): void
    {
        $user      = User::factory()->approved()->create();
        $quotation = Quotation::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('quotations.show', $quotation->quote_number))
            ->assertOk()
            ->assertViewIs('shop.quotations.show');
    }

        public function test_user_cannot_view_another_users_quotation(): void
    {
        $user1     = User::factory()->approved()->create();
        $user2     = User::factory()->approved()->create();
        $quotation = Quotation::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user1)
            ->get(route('quotations.show', $quotation->quote_number))
            ->assertNotFound();
    }

    // -------------------------------------------------------
    // Accept Quotation
    // -------------------------------------------------------

        public function test_user_can_accept_a_quoted_quotation(): void
    {
        $user      = User::factory()->approved()->create();
        $quotation = Quotation::factory()->quoted()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patch(route('quotations.accept', $quotation->quote_number))
            ->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'id'     => $quotation->id,
            'status' => 'accepted',
        ]);
    }

        public function test_user_cannot_accept_a_pending_quotation(): void
    {
        $user      = User::factory()->approved()->create();
        $quotation = Quotation::factory()->create([
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);

        $this->actingAs($user)
            ->patch(route('quotations.accept', $quotation->quote_number))
            ->assertNotFound();
    }

        public function test_user_cannot_accept_another_users_quotation(): void
    {
        $user1     = User::factory()->approved()->create();
        $user2     = User::factory()->approved()->create();
        $quotation = Quotation::factory()->quoted()->create(['user_id' => $user2->id]);

        $this->actingAs($user1)
            ->patch(route('quotations.accept', $quotation->quote_number))
            ->assertNotFound();
    }
}
