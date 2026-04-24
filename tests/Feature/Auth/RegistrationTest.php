<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Registration Form
    // -------------------------------------------------------

        public function test_guest_can_view_registration_page(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertViewIs('auth.register');
    }

        public function test_authenticated_user_is_redirected_away_from_register(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('register'))
            ->assertRedirect();
    }

    // -------------------------------------------------------
    // Successful Registration
    // -------------------------------------------------------

        public function test_user_can_register_with_valid_data(): void
    {
        $payload = [
            'name'                  => 'Joan Garcia',
            'email'                 => 'joan@empresa.cat',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'company_name'          => 'Empresa SL',
            'cif'                   => 'B12345674',
            'phone'                 => '612345678',
            'address'               => 'Carrer Major 10',
            'city'                  => 'Barcelona',
            'postal_code'           => '08001',
            'country'               => 'ES',
        ];

        $this->post(route('register.post'), $payload)
            ->assertRedirect(route('pending'));

        $this->assertDatabaseHas('users', [
            'email' => 'joan@empresa.cat',
            'role'  => 'pending',
        ]);
    }

        public function test_newly_registered_user_has_pending_role(): void
    {
        $payload = [
            'name'                  => 'Test User',
            'email'                 => 'test@test.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'company_name'          => 'Test SL',
            'cif'                   => 'B87654323',
            'phone'                 => '600000000',
            'address'               => 'Carrer Test 1',
            'city'                  => 'Madrid',
            'postal_code'           => '28001',
            'country'               => 'ES',
        ];

        $this->post(route('register.post'), $payload);

        $user = User::where('email', 'test@test.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals('pending', $user->role);
    }

    // -------------------------------------------------------
    // Validation
    // -------------------------------------------------------

        public function test_registration_fails_without_required_fields(): void
    {
        $this->post(route('register.post'), [])
            ->assertSessionHasErrors([
                'name',
                'email',
                'password',
                'company_name',
                'cif',
            ]);
    }

        public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $this->post(route('register.post'), [
            'name'                  => 'Another User',
            'email'                 => 'existing@test.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'company_name'          => 'Another SL',
            'cif'                   => 'B11111119',
            'phone'                 => '600000001',
            'address'               => 'Carrer Test 2',
            'city'                  => 'Valencia',
            'postal_code'           => '46001',
            'country'               => 'ES',
        ])->assertSessionHasErrors(['email']);
    }

        public function test_registration_fails_when_passwords_do_not_match(): void
    {
        $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'mismatch@test.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'WrongPassword!',
            'company_name'          => 'Test SL',
            'cif'                   => 'B22222228',
        ])->assertSessionHasErrors(['password']);
    }

        public function test_registration_fails_with_invalid_email_format(): void
    {
        $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'not-an-email',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'company_name'          => 'Test SL',
            'cif'                   => 'B33333337',
        ])->assertSessionHasErrors(['email']);
    }

        public function test_registration_fails_with_weak_password(): void
    {
        $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'weak@test.com',
            'password'              => '123',
            'password_confirmation' => '123',
            'company_name'          => 'Test SL',
            'cif'                   => 'B44444446',
        ])->assertSessionHasErrors(['password']);
    }
}
