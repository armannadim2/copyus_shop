<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_admin_can_access_orders_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.orders.index'))
             ->assertOk()
             ->assertViewIs('admin.orders.index');
    }

        public function test_b2b_user_cannot_access_admin_orders(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.orders.index'))
             ->assertForbidden();
    }

    // -------------------------------------------------------
    // Filter Orders
    // -------------------------------------------------------

        public function test_admin_can_filter_orders_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->count(3)->create(['status' => 'pending']);
        Order::factory()->count(2)->delivered()->create();

        $this->actingAs($admin)
             ->get(route('admin.orders.index', ['status' => 'pending']))
             ->assertOk()
             ->assertViewHas('orders');
    }

        public function test_admin_can_search_orders_by_order_number(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->create(['order_number' => 'ORD-FINDME']);
        Order::factory()->count(3)->create();

        $this->actingAs($admin)
             ->get(route('admin.orders.index', ['search' => 'ORD-FINDME']))
             ->assertOk()
             ->assertSee('ORD-FINDME');
    }

    // -------------------------------------------------------
    // View & Update
    // -------------------------------------------------------

        public function test_admin_can_view_order_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $this->actingAs($admin)
             ->get(route('admin.orders.show', $order->order_number))
             ->assertOk()
             ->assertViewIs('admin.orders.show');
    }

        public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
             ->patch(route('admin.orders.status', $order->order_number), [
                 'status' => 'confirmed',
             ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'confirmed',
        ]);
    }

        public function test_admin_cannot_set_invalid_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
             ->patch(route('admin.orders.status', $order->order_number), [
                 'status' => 'invalid_status',
             ])->assertSessionHasErrors(['status']);
    }
}
