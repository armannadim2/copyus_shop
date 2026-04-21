<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserCompanyRoleTest extends TestCase
{
        public function test_owner_is_company_owner(): void
    {
        $user = User::factory()->make(['company_role' => 'owner']);
        $this->assertTrue($user->isCompanyOwner());
    }

        public function test_manager_is_not_company_owner(): void
    {
        $user = User::factory()->make(['company_role' => 'manager']);
        $this->assertFalse($user->isCompanyOwner());
    }

        public function test_owner_is_company_manager(): void
    {
        $user = User::factory()->make(['company_role' => 'owner']);
        $this->assertTrue($user->isCompanyManager());
    }

        public function test_manager_is_company_manager(): void
    {
        $user = User::factory()->make(['company_role' => 'manager']);
        $this->assertTrue($user->isCompanyManager());
    }

        public function test_buyer_is_not_company_manager(): void
    {
        $user = User::factory()->make(['company_role' => 'buyer']);
        $this->assertFalse($user->isCompanyManager());
    }

        public function test_owner_can_manage_company(): void
    {
        $user = User::factory()->make(['company_role' => 'owner']);
        $this->assertTrue($user->canManageCompany());
    }

        public function test_buyer_cannot_manage_company(): void
    {
        $user = User::factory()->make(['company_role' => 'buyer']);
        $this->assertFalse($user->canManageCompany());
    }

        public function test_owner_can_place_orders(): void
    {
        $user = User::factory()->make(['company_role' => 'owner']);
        $this->assertTrue($user->canPlaceOrders());
    }

        public function test_buyer_can_place_orders(): void
    {
        $user = User::factory()->make(['company_role' => 'buyer']);
        $this->assertTrue($user->canPlaceOrders());
    }

        public function test_user_without_company_role_can_place_orders(): void
    {
        $user = User::factory()->make(['company_role' => null]);
        $this->assertTrue($user->canPlaceOrders());
    }

        public function test_admin_and_approved_users_can_see_prices(): void
    {
        $admin   = User::factory()->admin()->make();
        $approved = User::factory()->approved()->make();

        $this->assertTrue($admin->canSeePrices());
        $this->assertTrue($approved->canSeePrices());
    }

        public function test_pending_user_cannot_see_prices(): void
    {
        $user = User::factory()->pending()->make();
        $this->assertFalse($user->canSeePrices());
    }

        public function test_rejected_user_cannot_see_prices(): void
    {
        $user = User::factory()->rejected()->make();
        $this->assertFalse($user->canSeePrices());
    }
}
