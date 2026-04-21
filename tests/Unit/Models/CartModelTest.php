<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class CartModelTest extends TestCase
{
        public function test_it_calculates_subtotal_correctly(): void
    {
        $user = User::factory()->approved()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 2,
            'unit_price' => 50.0000,
        ]);

        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 1,
            'unit_price' => 30.0000,
        ]);

        $cart->load('items');

        $this->assertEquals(130.00, $cart->subtotal);
    }

        public function test_it_counts_items_correctly(): void
    {
        $user = User::factory()->approved()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 3,
            'unit_price' => 10.00,
        ]);

        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 2,
            'unit_price' => 10.00,
        ]);

        $cart->load('items');

        $this->assertEquals(5, $cart->item_count);
    }

        public function test_it_detects_empty_cart(): void
    {
        $user = User::factory()->approved()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->load('items');

        $this->assertTrue($cart->is_empty);
    }

        public function test_it_detects_non_empty_cart(): void
    {
        $user = User::factory()->approved()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 1,
            'unit_price' => 10.00,
        ]);

        $cart->load('items');

        $this->assertFalse($cart->is_empty);
    }
}
