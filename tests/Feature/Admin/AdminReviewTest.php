<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReviewTest extends TestCase
{
    use RefreshDatabase;

    private function makeReview(array $overrides = []): ProductReview
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        return ProductReview::create(array_merge([
            'user_id'     => $user->id,
            'product_id'  => $product->id,
            'rating'      => 4,
            'body'        => 'Test review.',
            'is_approved' => false,
        ], $overrides));
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_admin_reviews(): void
    {
        $this->get(route('admin.reviews.index'))
             ->assertRedirect(route('login'));
    }

        public function test_b2b_user_cannot_access_admin_reviews(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.reviews.index'))
             ->assertForbidden();
    }

        public function test_admin_can_view_reviews_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.reviews.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Approve Review
    // -------------------------------------------------------

        public function test_admin_can_approve_a_review(): void
    {
        $admin  = User::factory()->admin()->create();
        $review = $this->makeReview(['is_approved' => false]);

        $this->actingAs($admin)
             ->patch(route('admin.reviews.approve', $review->id))
             ->assertRedirect();

        $this->assertDatabaseHas('product_reviews', [
            'id'          => $review->id,
            'is_approved' => true,
        ]);
    }

    // -------------------------------------------------------
    // Reject / Delete Review
    // -------------------------------------------------------

        public function test_admin_can_delete_a_review(): void
    {
        $admin  = User::factory()->admin()->create();
        $review = $this->makeReview();

        $this->actingAs($admin)
             ->delete(route('admin.reviews.destroy', $review->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
    }

    // -------------------------------------------------------
    // Filter Reviews
    // -------------------------------------------------------

        public function test_admin_can_filter_pending_reviews(): void
    {
        $admin = User::factory()->admin()->create();

        $this->makeReview(['is_approved' => false]);
        $this->makeReview(['is_approved' => true]);

        $this->actingAs($admin)
             ->get(route('admin.reviews.index', ['status' => 'pending']))
             ->assertOk()
             ->assertViewHas('reviews');
    }
}
