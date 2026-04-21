<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
        public function test_it_returns_correct_status_color(): void
    {
        $statuses = [
            'pending'    => 'bg-yellow-50 text-yellow-700',
            'confirmed'  => 'bg-blue-50 text-blue-700',
            'delivered'  => 'bg-green-50 text-green-700',
            'cancelled'  => 'bg-red-50 text-red-600',
        ];

        foreach ($statuses as $status => $expected) {
            $order = Order::factory()->make(['status' => $status]);
            $this->assertEquals($expected, $order->status_color);
        }
    }

        public function test_it_is_editable_only_when_pending(): void
    {
        $pending   = Order::factory()->make(['status' => 'pending']);
        $confirmed = Order::factory()->make(['status' => 'confirmed']);

        $this->assertTrue($pending->is_editable);
        $this->assertFalse($confirmed->is_editable);
    }

        public function test_it_is_cancellable_when_pending_or_confirmed(): void
    {
        $pending   = Order::factory()->make(['status' => 'pending']);
        $confirmed = Order::factory()->make(['status' => 'confirmed']);
        $shipped   = Order::factory()->make(['status' => 'shipped']);

        $this->assertTrue($pending->is_cancellable);
        $this->assertTrue($confirmed->is_cancellable);
        $this->assertFalse($shipped->is_cancellable);
    }

        public function test_it_counts_total_items(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 3,
        ]);

        OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => Product::factory()->create()->id,
            'quantity'   => 2,
        ]);

        $order->load('items');

        $this->assertEquals(5, $order->item_count);
    }

        public function test_it_scopes_pending_orders(): void
    {
        Order::factory()->count(2)->create(['status' => 'pending']);
        Order::factory()->count(3)->create(['status' => 'delivered']);

        $this->assertCount(2, Order::pending()->get());
    }

        public function test_it_scopes_delivered_orders(): void
    {
        Order::factory()->count(2)->delivered()->create();
        Order::factory()->count(3)->create(['status' => 'pending']);

        $this->assertCount(2, Order::delivered()->get());
    }
}
