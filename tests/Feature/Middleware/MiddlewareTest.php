<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // IsAdmin Middleware
    // -------------------------------------------------------

        public function test_admin_middleware_allows_admin_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk();
    }

        public function test_admin_middleware_blocks_b2b_users(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('admin.index'))
            ->assertForbidden();
    }

        public function test_admin_middleware_blocks_pending_users(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('admin.index'))
            ->assertForbidden();
    }

        public function test_admin_middleware_redirects_guests(): void
    {
        $this->get(route('admin.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------
    // IsApprovedB2B Middleware
    // -------------------------------------------------------

        public function test_b2b_middleware_allows_approved_users(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

        public function test_b2b_middleware_blocks_pending_users(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

        public function test_b2b_middleware_blocks_rejected_users(): void
    {
        $user = User::factory()->rejected()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

        public function test_b2b_middleware_redirects_guests_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------
    // IsActive Middleware
    // -------------------------------------------------------

        public function test_inactive_approved_user_is_blocked(): void
    {
        $user = User::factory()->approved()->create(['is_active' => false]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

        public function test_active_approved_user_is_allowed(): void
    {
        $user = User::factory()->approved()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    // -------------------------------------------------------
    // SetLocale Middleware
    // -------------------------------------------------------

        public function test_locale_is_set_from_authenticated_user_preference(): void
    {
        $user = User::factory()->approved()->create(['locale' => 'es']);

        $this->actingAs($user)
            ->get(route('dashboard'));

        $this->assertEquals('es', app()->getLocale());
    }

        public function test_locale_defaults_to_ca_for_guests(): void
    {
        $this->get(route('products.index'));

        $this->assertEquals('ca', app()->getLocale());
    }

        public function test_locale_can_be_switched_via_query_param(): void
    {
        $user = User::factory()->approved()->create(['locale' => 'ca']);

        $this->actingAs($user)
            ->get(route('locale.switch', ['locale' => 'en']))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'locale' => 'en',
        ]);
    }

    // -------------------------------------------------------
    // Guest-Only Routes
    // -------------------------------------------------------

        public function test_authenticated_approved_user_is_redirected_from_login(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

        public function test_authenticated_admin_is_redirected_from_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('login'))
            ->assertRedirect(route('admin.index'));
    }

        public function test_authenticated_pending_user_is_redirected_from_login(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('pending'));
    }
}
