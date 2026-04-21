<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderExtendedTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Tracking Number
    // -------------------------------------------------------

        public function test_admin_can_add_tracking_number_to_order(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => 'confirmed']);

        $this->actingAs($admin)
             ->patch(route('admin.orders.tracking', $order->order_number), [
                 'tracking_number'  => 'GLS-123456789',
                 'tracking_carrier' => 'GLS',
             ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'              => $order->id,
            'tracking_number' => 'GLS-123456789',
        ]);
    }

        public function test_tracking_update_requires_tracking_number(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => 'confirmed']);

        $this->actingAs($admin)
             ->patch(route('admin.orders.tracking', $order->order_number), [])
             ->assertSessionHasErrors('tracking_number');
    }

    // -------------------------------------------------------
    // Mark as Paid
    // -------------------------------------------------------

        public function test_admin_can_mark_order_as_paid(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status'         => 'confirmed',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($admin)
             ->patch(route('admin.orders.paid', $order->order_number), [
                 'payment_reference' => 'WIRE-2026-001',
             ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id'             => $order->id,
            'payment_status' => 'paid',
        ]);
    }

        public function test_admin_sees_404_for_non_existent_order(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.orders.show', 'ORD-DOESNOTEXIST'))
             ->assertNotFound();
    }

    // -------------------------------------------------------
    // Status — full lifecycle
    // -------------------------------------------------------

        public function test_admin_can_advance_order_from_pending_to_shipped(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => 'pending']);

        foreach (['confirmed', 'processing', 'shipped'] as $status) {
            $this->actingAs($admin)
                 ->patch(route('admin.orders.status', $order->order_number), [
                     'status' => $status,
                 ])->assertRedirect();

            $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => $status]);
        }
    }

        public function test_admin_can_filter_orders_by_payment_status(): void
    {
        $admin = User::factory()->admin()->create();

        Order::factory()->count(2)->create(['payment_status' => 'paid']);
        Order::factory()->count(3)->create(['payment_status' => 'unpaid']);

        $this->actingAs($admin)
             ->get(route('admin.orders.index', ['payment_status' => 'unpaid']))
             ->assertOk()
             ->assertViewHas('orders');
    }
}
