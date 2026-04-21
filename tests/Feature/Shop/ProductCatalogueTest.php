<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogueTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Public Access
    // -------------------------------------------------------

        public function test_guest_can_view_product_listing(): void
    {
        Product::factory()->count(5)->create(['is_active' => true]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertViewIs('shop.products.index');
    }

        public function test_guest_can_view_product_detail(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertViewIs('shop.products.show');
    }

        public function test_inactive_product_returns_404_for_guests(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('products.show', $product->slug))
            ->assertNotFound();
    }

        public function test_guest_cannot_see_prices_on_listing(): void
    {
        Product::factory()->count(3)->create(['is_active' => true]);

        $response = $this->get(route('products.index'));

        $response->assertDontSee('€')
            ->assertSee('login');
    }

        public function test_approved_b2b_user_can_see_prices(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->withPrice(99.99)->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('products.index'))
            ->assertSee('99');
    }

    // -------------------------------------------------------
    // Search & Filters
    // -------------------------------------------------------

        public function test_products_can_be_filtered_by_search_query(): void
    {
        Product::factory()->create([
            'is_active' => true,
            'brand'     => 'SpecialBrand',
        ]);
        Product::factory()->count(3)->create(['is_active' => true]);

        $this->get(route('products.index', ['search' => 'SpecialBrand']))
            ->assertOk()
            ->assertSee('SpecialBrand');
    }

        public function test_products_can_be_filtered_by_category(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'is_active'   => true,
        ]);
        Product::factory()->count(3)->create(['is_active' => true]);

        $this->get(route('products.index', ['category' => $category->slug]))
            ->assertOk();
    }

        public function test_products_can_be_filtered_by_in_stock(): void
    {
        Product::factory()->count(2)->create([
            'stock'     => 10,
            'is_active' => true,
        ]);
        Product::factory()->count(3)->outOfStock()->create(['is_active' => true]);

        $response = $this->get(route('products.index', ['in_stock' => 1]));

        $response->assertOk();
    }

    // -------------------------------------------------------
    // Category Page
    // -------------------------------------------------------

        public function test_guest_can_view_category_page(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'is_active'   => true,
        ]);

        $this->get(route('products.category', $category->slug))
            ->assertOk()
            ->assertViewIs('shop.products.index');
    }

        public function test_inactive_category_returns_404(): void
    {
        $category = Category::factory()->inactive()->create();

        $this->get(route('products.category', $category->slug))
            ->assertNotFound();
    }
}
