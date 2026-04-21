<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_admin_reports(): void
    {
        $this->get(route('admin.reports.index'))
             ->assertRedirect(route('login'));
    }

        public function test_b2b_user_cannot_access_admin_reports(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.reports.index'))
             ->assertForbidden();
    }

        public function test_admin_can_view_reports_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.reports.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Revenue Report
    // -------------------------------------------------------

        public function test_admin_can_view_revenue_report(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.reports.revenue'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Products Report
    // -------------------------------------------------------

        public function test_admin_can_view_products_report(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.reports.products'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Clients Report
    // -------------------------------------------------------

        public function test_admin_can_view_clients_report(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.reports.clients'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Print Jobs Report
    // -------------------------------------------------------

        public function test_admin_can_view_print_jobs_report(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.reports.print-jobs'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Export Orders
    // -------------------------------------------------------

        public function test_admin_can_export_orders(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->count(3)->create();

        $response = $this->actingAs($admin)
             ->get(route('admin.reports.export.orders'));

        // Should return a downloadable file (200 or streamed)
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }
}
