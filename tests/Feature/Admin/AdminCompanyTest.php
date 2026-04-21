<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCompanyTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(array $overrides = []): Company
    {
        return Company::create(array_merge([
            'name'      => 'Test Company ' . uniqid(),
            'is_active' => true,
        ], $overrides));
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_admin_companies(): void
    {
        $this->get(route('admin.companies.index'))
             ->assertRedirect(route('login'));
    }

        public function test_b2b_user_cannot_access_admin_companies(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.companies.index'))
             ->assertForbidden();
    }

        public function test_admin_can_view_companies_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.companies.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // View Company
    // -------------------------------------------------------

        public function test_admin_can_view_company_detail(): void
    {
        $admin   = User::factory()->admin()->create();
        $company = $this->makeCompany();

        $this->actingAs($admin)
             ->get(route('admin.companies.show', $company->id))
             ->assertOk();
    }

        public function test_admin_sees_404_for_non_existent_company(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.companies.show', 99999))
             ->assertNotFound();
    }

    // -------------------------------------------------------
    // Update Company
    // -------------------------------------------------------

        public function test_admin_can_update_company_payment_terms(): void
    {
        $admin   = User::factory()->admin()->create();
        $company = $this->makeCompany(['payment_terms' => 'immediate']);

        $this->actingAs($admin)
             ->patch(route('admin.companies.update', $company->id), [
                 'payment_terms' => 'net_30',
                 'credit_limit'  => 5000.00,
             ])->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'id'            => $company->id,
            'payment_terms' => 'net_30',
        ]);
    }

        public function test_admin_can_update_company_credit_limit(): void
    {
        $admin   = User::factory()->admin()->create();
        $company = $this->makeCompany(['credit_limit' => 0]);

        $this->actingAs($admin)
             ->patch(route('admin.companies.update', $company->id), [
                 'credit_limit' => 10000.00,
             ])->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'id'           => $company->id,
            'credit_limit' => 10000.00,
        ]);
    }
}
