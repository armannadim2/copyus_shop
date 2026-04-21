<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Login Page
    // -------------------------------------------------------

        public function test_guest_can_view_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login');
    }

        public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect();
    }

    // -------------------------------------------------------
    // Successful Login
    // -------------------------------------------------------

        public function test_approved_user_can_login_and_reach_dashboard(): void
    {
        $user = User::factory()->approved()->create([
            'email'    => 'approved@test.com',
            'password' => bcrypt('Password1!'),
        ]);

        $this->post(route('login.post'), [
            'email'    => 'approved@test.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

        public function test_admin_user_can_login_and_reach_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email'    => 'admin@test.com',
            'password' => bcrypt('AdminPass1!'),
        ]);

        $this->post(route('login.post'), [
            'email'    => 'admin@test.com',
            'password' => 'AdminPass1!',
        ])->assertRedirect(route('admin.index'));

        $this->assertAuthenticatedAs($admin);
    }

        public function test_pending_user_is_redirected_to_pending_page(): void
    {
        User::factory()->pending()->create([
            'email'    => 'pending@test.com',
            'password' => bcrypt('Password1!'),
        ]);

        $this->post(route('login.post'), [
            'email'    => 'pending@test.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('pending'));
    }

        public function test_rejected_user_is_redirected_to_rejected_page(): void
    {
        User::factory()->rejected()->create([
            'email'    => 'rejected@test.com',
            'password' => bcrypt('Password1!'),
        ]);

        $this->post(route('login.post'), [
            'email'    => 'rejected@test.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('rejected'));
    }

    // -------------------------------------------------------
    // Failed Login
    // -------------------------------------------------------

        public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->approved()->create([
            'email'    => 'valid@test.com',
            'password' => bcrypt('CorrectPass1!'),
        ]);

        $this->post(route('login.post'), [
            'email'    => 'valid@test.com',
            'password' => 'WrongPass!',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

        public function test_login_fails_with_non_existent_email(): void
    {
        $this->post(route('login.post'), [
            'email'    => 'nobody@test.com',
            'password' => 'Password1!',
        ])->assertSessionHasErrors(['email']);
    }

        public function test_login_fails_without_credentials(): void
    {
        $this->post(route('login.post'), [])
            ->assertSessionHasErrors(['email', 'password']);
    }

    // -------------------------------------------------------
    // Logout
    // -------------------------------------------------------

        public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

        public function test_guest_cannot_logout(): void
    {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    }
}
