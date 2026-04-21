<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_admin_can_access_users_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewIs('admin.users.index');
    }

        public function test_b2b_user_cannot_access_admin_users_list(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

        public function test_guest_cannot_access_admin_users_list(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------
    // User List
    // -------------------------------------------------------

        public function test_admin_can_filter_users_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->pending()->create();
        User::factory()->count(2)->approved()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => 'pending']))
            ->assertOk()
            ->assertViewHas('users');
    }

        public function test_admin_can_search_users_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'UniqueSearchName', 'role' => 'approved']);
        User::factory()->count(3)->approved()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'UniqueSearchName']));

        $response->assertOk()
            ->assertSee('UniqueSearchName');
    }

    // -------------------------------------------------------
    // View User
    // -------------------------------------------------------

        public function test_admin_can_view_user_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.show', $user->id))
            ->assertOk()
            ->assertViewIs('admin.users.show');
    }

        public function test_admin_sees_404_for_non_existent_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.show', 99999))
            ->assertNotFound();
    }

    // -------------------------------------------------------
    // Approve / Reject User
    // -------------------------------------------------------

        public function test_admin_can_approve_a_pending_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.approve', $user->id))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'role' => 'approved',
        ]);
    }

        public function test_admin_can_reject_a_pending_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.reject', $user->id))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'role' => 'rejected',
        ]);
    }

    // -------------------------------------------------------
    // Delete User
    // -------------------------------------------------------

        public function test_admin_can_delete_a_rejected_user(): void
    {
        $admin   = User::factory()->admin()->create();
        $rejected = User::factory()->rejected()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $rejected->id))
            ->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $rejected->id]);
    }
}
