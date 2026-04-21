<?php

namespace Tests\Unit\Models;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // is_print_job
    // -------------------------------------------------------

        public function test_product_cart_item_is_not_a_print_job(): void
    {
        $item = CartItem::factory()->make([
            'print_job_id' => null,
            'product_id'   => 1,
        ]);

        $this->assertFalse($item->is_print_job);
    }

        public function test_cart_item_with_print_job_id_is_a_print_job(): void
    {
        $item = CartItem::factory()->make([
            'print_job_id' => 42,
            'product_id'   => null,
        ]);

        $this->assertTrue($item->is_print_job);
    }

    // -------------------------------------------------------
    // effective_unit_price
    // -------------------------------------------------------

        public function test_product_item_uses_product_price_as_effective_unit_price(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['price' => 50.00, 'vat_rate' => 21.00]);

        $item = CartItem::factory()->create([
            'user_id'      => $user->id,
            'product_id'   => $product->id,
            'print_job_id' => null,
            'quantity'     => 1,
            'unit_price'   => null,
            'type'         => 'cart',
        ]);

        $item->load('product');

        $this->assertEquals(50.00, $item->effective_unit_price);
    }

    // -------------------------------------------------------
    // line_total (product item)
    // -------------------------------------------------------

        public function test_line_total_includes_vat_for_product_item(): void
    {
        $user    = User::factory()->approved()->create();
        $product = Product::factory()->create(['price' => 100.00, 'vat_rate' => 21.00]);

        $item = CartItem::factory()->create([
            'user_id'      => $user->id,
            'product_id'   => $product->id,
            'print_job_id' => null,
            'quantity'     => 2,
            'unit_price'   => null,
            'type'         => 'cart',
        ]);

        $item->load('product');

        // 100.00 * 1.21 * 2 = 242.00
        $this->assertEquals(242.00, $item->line_total);
    }
}
