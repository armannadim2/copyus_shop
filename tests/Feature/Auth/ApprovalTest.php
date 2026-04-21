<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    use RefreshDatabase;

        public function test_admin_can_approve_pending_user(): void
    {
        $admin   = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.approve', $pending->id))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'   => $pending->id,
            'role' => 'approved',
        ]);
    }

        public function test_admin_can_reject_pending_user(): void
    {
        $admin   = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.reject', $pending->id))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'   => $pending->id,
            'role' => 'rejected',
        ]);
    }

        public function test_non_admin_cannot_approve_users(): void
    {
        $b2b     = User::factory()->approved()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($b2b)
            ->patch(route('admin.users.approve', $pending->id))
            ->assertForbidden();
    }

        public function test_non_admin_cannot_reject_users(): void
    {
        $b2b     = User::factory()->approved()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($b2b)
            ->patch(route('admin.users.reject', $pending->id))
            ->assertForbidden();
    }

        public function test_guest_cannot_approve_users(): void
    {
        $pending = User::factory()->pending()->create();

        $this->patch(route('admin.users.approve', $pending->id))
            ->assertRedirect(route('login'));
    }

        public function test_approved_at_timestamp_is_set_on_approval(): void
    {
        $admin   = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.approve', $pending->id));

        $this->assertNotNull($pending->fresh()->approved_at);
    }

        public function test_pending_page_is_accessible_by_pending_user(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('pending'))
            ->assertOk();
    }

        public function test_rejected_page_is_accessible_by_rejected_user(): void
    {
        $rejected = User::factory()->rejected()->create();

        $this->actingAs($rejected)
            ->get(route('rejected'))
            ->assertOk();
    }
}
