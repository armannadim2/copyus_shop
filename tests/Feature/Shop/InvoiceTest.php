<?php

namespace Tests\Feature\Shop;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_view_invoices(): void
    {
        $this->get(route('invoices.index'))
             ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_view_invoices(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
             ->get(route('invoices.index'))
             ->assertForbidden();
    }

        public function test_approved_user_can_view_their_invoices(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('invoices.index'))
             ->assertOk()
             ->assertViewIs('shop.invoices.index');
    }

    // -------------------------------------------------------
    // Invoice List
    // -------------------------------------------------------

        public function test_user_only_sees_their_own_invoices(): void
    {
        $user1 = User::factory()->approved()->create();
        $user2 = User::factory()->approved()->create();

        $order1 = Order::factory()->create(['user_id' => $user1->id]);
        $order2 = Order::factory()->create(['user_id' => $user2->id]);

        Invoice::factory()->create(['user_id' => $user1->id, 'order_id' => $order1->id]);
        Invoice::factory()->create(['user_id' => $user2->id, 'order_id' => $order2->id]);

        $response = $this->actingAs($user1)
             ->get(route('invoices.index'));

        $response->assertOk()
                 ->assertViewHas('invoices', function ($invoices) use ($user1, $user2) {
                     return $invoices->every(fn($inv) => $inv->user_id === $user1->id);
                 });
    }

    // -------------------------------------------------------
    // Download Invoice
    // -------------------------------------------------------

        public function test_user_can_download_their_own_invoice(): void
    {
        $user  = User::factory()->approved()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $inv   = Invoice::factory()->create(['user_id' => $user->id, 'order_id' => $order->id]);

        $response = $this->actingAs($user)
             ->get(route('invoices.download', $inv->id));

        // PDF download should succeed (200) or redirect
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

        public function test_user_cannot_download_another_users_invoice(): void
    {
        $user1 = User::factory()->approved()->create();
        $user2 = User::factory()->approved()->create();
        $order = Order::factory()->create(['user_id' => $user2->id]);
        $inv   = Invoice::factory()->create(['user_id' => $user2->id, 'order_id' => $order->id]);

        $this->actingAs($user1)
             ->get(route('invoices.download', $inv->id))
             ->assertNotFound();
    }
}
