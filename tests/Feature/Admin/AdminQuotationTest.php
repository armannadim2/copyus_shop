<?php

namespace Tests\Feature\Admin;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuotationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_admin_can_access_quotations_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.quotations.index'))
            ->assertOk()
            ->assertViewIs('admin.quotations.index');
    }

        public function test_b2b_user_cannot_access_admin_quotations(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('admin.quotations.index'))
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // View & Price Quotation
    // -------------------------------------------------------

        public function test_admin_can_view_quotation_detail(): void
    {
        $admin     = User::factory()->admin()->create();
        $quotation = Quotation::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.quotations.show', $quotation->quote_number))
            ->assertOk()
            ->assertViewIs('admin.quotations.show');
    }

        public function test_admin_can_submit_quoted_prices_for_quotation(): void
    {
        $admin     = User::factory()->admin()->create();
        $user      = User::factory()->approved()->create();
        $quotation = Quotation::factory()->create([
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);

        $product = Product::factory()->create(['is_active' => true]);

        $item = QuotationItem::factory()->create([
            'quotation_id' => $quotation->id,
            'product_id'   => $product->id,
            'quantity'     => 5,
            'unit_price'   => 10.00,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.quotations.price', $quotation->quote_number), [
                'admin_notes' => 'Ready for you.',
                'valid_until' => now()->addDays(30)->toDateString(),
                'items'       => [
                    ['id' => $item->id, 'price' => 8.50],
                ],
            ])->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'id'     => $quotation->id,
            'status' => 'quoted',
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'id'           => $item->id,
            'quoted_price' => 8.50,
        ]);
    }

        public function test_quote_submission_fails_without_valid_until(): void
    {
        $admin     = User::factory()->admin()->create();
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.quotations.price', $quotation->quote_number), [
                'items' => [],
            ])->assertSessionHasErrors(['valid_until']);
    }

        public function test_quote_submission_fails_with_past_valid_until_date(): void
    {
        $admin     = User::factory()->admin()->create();
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.quotations.price', $quotation->quote_number), [
                'valid_until' => now()->subDay()->toDateString(),
                'items'       => [],
            ])->assertSessionHasErrors(['valid_until']);
    }

    // -------------------------------------------------------
    // Status Transitions
    // -------------------------------------------------------

        public function test_admin_can_mark_quotation_as_reviewing(): void
    {
        $admin     = User::factory()->admin()->create();
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.quotations.status', $quotation->quote_number), [
                'status' => 'reviewing',
            ])->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'id'     => $quotation->id,
            'status' => 'reviewing',
        ]);
    }

        public function test_admin_can_reject_a_pending_quotation(): void
    {
        $admin     = User::factory()->admin()->create();
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.quotations.status', $quotation->quote_number), [
                'status' => 'rejected',
            ])->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'id'     => $quotation->id,
            'status' => 'rejected',
        ]);
    }

        public function test_admin_cannot_set_invalid_quotation_status(): void
    {
        $admin     = User::factory()->admin()->create();
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.quotations.status', $quotation->quote_number), [
                'status' => 'flying',
            ])->assertSessionHasErrors(['status']);
    }
}
