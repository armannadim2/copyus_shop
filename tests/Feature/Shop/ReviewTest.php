<?php

namespace Tests\Feature\Shop;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_submit_review(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->post(route('reviews.store', $product->slug), [
            'rating' => 5,
            'body'   => 'Great product!',
        ])->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_submit_review(): void
    {
        $user    = User::factory()->pending()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('reviews.store', $product->slug), [
                 'rating' => 5,
                 'body'   => 'Great!',
             ])->assertForbidden();
    }

    // -------------------------------------------------------
    // Submit Review
    // -------------------------------------------------------

        public function test_approved_user_can_submit_a_review(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('reviews.store', $product->slug), [
                 'rating' => 4,
                 'title'  => 'Good value',
                 'body'   => 'Would recommend.',
             ])->assertRedirect();

        $this->assertDatabaseHas('product_reviews', [
            'user_id'     => $user->id,
            'product_id'  => $product->id,
            'rating'      => 4,
            'is_approved' => false,
        ]);
    }

        public function test_review_is_not_auto_approved_on_submission(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('reviews.store', $product->slug), [
                 'rating' => 5,
                 'body'   => 'Excellent!',
             ]);

        $review = ProductReview::where('user_id', $user->id)->first();
        $this->assertFalse((bool) $review->is_approved);
    }

        public function test_review_requires_rating_and_body(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('reviews.store', $product->slug), [])
             ->assertSessionHasErrors(['rating', 'body']);
    }

        public function test_rating_must_be_between_1_and_5(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
             ->post(route('reviews.store', $product->slug), ['rating' => 6, 'body' => 'test'])
             ->assertSessionHasErrors('rating');

        $this->actingAs($user)
             ->post(route('reviews.store', $product->slug), ['rating' => 0, 'body' => 'test'])
             ->assertSessionHasErrors('rating');
    }

    // -------------------------------------------------------
    // Delete Review
    // -------------------------------------------------------

        public function test_user_can_delete_their_own_review(): void
    {
        $user   = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $review = ProductReview::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'rating'     => 3,
            'body'       => 'Average.',
            'is_approved' => false,
        ]);

        $this->actingAs($user)
             ->delete(route('reviews.destroy', $review->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
    }

        public function test_user_cannot_delete_another_users_review(): void
    {
        $user1  = User::factory()->approved()->create();
        $user2  = User::factory()->approved()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $review = ProductReview::create([
            'user_id'    => $user2->id,
            'product_id' => $product->id,
            'rating'     => 5,
            'body'       => 'Love it.',
            'is_approved' => true,
        ]);

        $this->actingAs($user1)
             ->delete(route('reviews.destroy', $review->id))
             ->assertNotFound();

        $this->assertDatabaseHas('product_reviews', ['id' => $review->id]);
    }
}
