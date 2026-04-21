<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
        public function test_it_calculates_price_with_vat(): void
    {
        $product = Product::factory()->make([
            'price'    => 100.00,
            'vat_rate' => 21.00,
        ]);

        $this->assertEquals(121.00, $product->price_with_vat);
    }

        public function test_it_calculates_vat_amount(): void
    {
        $product = Product::factory()->make([
            'price'    => 100.00,
            'vat_rate' => 21.00,
        ]);

        $this->assertEquals(21.00, $product->vat_amount);
    }

        public function test_it_detects_in_stock_correctly(): void
    {
        $inStock    = Product::factory()->make(['stock' => 10]);
        $outOfStock = Product::factory()->outOfStock()->make();

        $this->assertTrue($inStock->is_in_stock);
        $this->assertFalse($outOfStock->is_in_stock);
    }

        public function test_it_returns_image_url_when_image_set(): void
    {
        $product = Product::factory()->make(['image' => 'products/test.jpg']);
        $this->assertStringContainsString('products/test.jpg', $product->image_url);
    }

        public function test_it_returns_null_image_url_when_no_image(): void
    {
        $product = Product::factory()->make(['image' => null]);
        $this->assertNull($product->image_url);
    }

        public function test_it_scopes_active_products(): void
    {
        Product::factory()->count(3)->create(['is_active' => true]);
        Product::factory()->count(2)->inactive()->create();

        $active = Product::active()->get();

        $this->assertCount(3, $active);
    }

        public function test_it_scopes_featured_products(): void
    {
        Product::factory()->count(2)->featured()->create();
        Product::factory()->count(3)->create(['is_featured' => false]);

        $featured = Product::featured()->get();

        $this->assertCount(2, $featured);
    }

        public function test_it_scopes_in_stock_products(): void
    {
        Product::factory()->count(3)->create(['stock' => 10]);
        Product::factory()->count(2)->outOfStock()->create();

        $inStock = Product::inStock()->get();

        $this->assertCount(3, $inStock);
    }
}
