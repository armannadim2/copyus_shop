<?php

namespace Tests\Feature\Admin;

use App\Models\PrintJob;
use App\Models\PrintTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrintJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeTemplate(): PrintTemplate
    {
        return PrintTemplate::create([
            'slug'                 => 'flyers-' . uniqid(),
            'name'                 => ['ca' => 'Flyers', 'es' => 'Flyers', 'en' => 'Flyers'],
            'base_price'           => 10.00,
            'vat_rate'             => 21.00,
            'base_production_days' => 3,
            'is_active'            => true,
        ]);
    }

    private function makeJob(User $user, array $overrides = []): PrintJob
    {
        $template = $this->makeTemplate();

        return PrintJob::create(array_merge([
            'user_id'           => $user->id,
            'print_template_id' => $template->id,
            'status'            => 'ordered',
            'configuration'     => [],
            'quantity'          => 100,
            'unit_price'        => 10.00,
            'total_price'       => 1000.00,
            'production_days'   => 3,
        ], $overrides));
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_admin_print_jobs(): void
    {
        $this->get(route('admin.print.jobs.index'))
             ->assertRedirect(route('login'));
    }

        public function test_b2b_user_cannot_access_admin_print_jobs(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.print.jobs.index'))
             ->assertForbidden();
    }

        public function test_admin_can_view_print_jobs_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.print.jobs.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // View Job Detail
    // -------------------------------------------------------

        public function test_admin_can_view_any_print_job(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();
        $job   = $this->makeJob($user);

        $this->actingAs($admin)
             ->get(route('admin.print.jobs.show', $job->id))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Update Production Status
    // -------------------------------------------------------

        public function test_admin_can_update_print_job_status_to_in_production(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();
        $job   = $this->makeJob($user, ['status' => 'ordered']);

        $this->actingAs($admin)
             ->patch(route('admin.print.jobs.status', $job->id), [
                 'status' => 'in_production',
             ])->assertRedirect();

        $this->assertDatabaseHas('print_jobs', [
            'id'     => $job->id,
            'status' => 'in_production',
        ]);
    }

        public function test_admin_can_mark_print_job_as_completed(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();
        $job   = $this->makeJob($user, ['status' => 'in_production']);

        $this->actingAs($admin)
             ->patch(route('admin.print.jobs.status', $job->id), [
                 'status' => 'completed',
             ])->assertRedirect();

        $this->assertDatabaseHas('print_jobs', [
            'id'     => $job->id,
            'status' => 'completed',
        ]);
    }

    // -------------------------------------------------------
    // Set Delivery Date
    // -------------------------------------------------------

        public function test_admin_can_set_expected_delivery_date(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();
        $job   = $this->makeJob($user);

        $this->actingAs($admin)
             ->patch(route('admin.print.jobs.delivery', $job->id), [
                 'expected_delivery_at' => '2026-05-01',
             ])->assertRedirect();

        $this->assertDatabaseHas('print_jobs', [
            'id'                   => $job->id,
            'expected_delivery_at' => '2026-05-01',
        ]);
    }

    // -------------------------------------------------------
    // Bulk Status Update
    // -------------------------------------------------------

        public function test_admin_can_bulk_update_print_job_statuses(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();

        $job1 = $this->makeJob($user, ['status' => 'ordered']);
        $job2 = $this->makeJob($user, ['status' => 'ordered']);

        $this->actingAs($admin)
             ->post(route('admin.print.jobs.bulk-status'), [
                 'job_ids' => [$job1->id, $job2->id],
                 'status'  => 'in_production',
             ])->assertRedirect();

        $this->assertDatabaseHas('print_jobs', ['id' => $job1->id, 'status' => 'in_production']);
        $this->assertDatabaseHas('print_jobs', ['id' => $job2->id, 'status' => 'in_production']);
    }
}
