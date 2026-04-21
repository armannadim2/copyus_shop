<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(array $overrides = []): Company
    {
        return Company::create(array_merge([
            'name'      => 'Test Company',
            'is_active' => true,
        ], $overrides));
    }

    // -------------------------------------------------------
    // Payment terms
    // -------------------------------------------------------

        public function test_it_returns_correct_payment_days_for_net_30(): void
    {
        $company = Company::make(['payment_terms' => 'net_30']);
        $this->assertEquals(30, $company->payment_days);
    }

        public function test_it_returns_correct_payment_days_for_net_60(): void
    {
        $company = Company::make(['payment_terms' => 'net_60']);
        $this->assertEquals(60, $company->payment_days);
    }

        public function test_it_returns_zero_payment_days_for_immediate_payment(): void
    {
        $company = Company::make(['payment_terms' => 'immediate']);
        $this->assertEquals(0, $company->payment_days);
    }

        public function test_it_returns_correct_payment_terms_label(): void
    {
        $company = Company::make(['payment_terms' => 'net_30']);
        $this->assertEquals('Net 30', $company->payment_terms_label);
    }

        public function test_it_returns_immediate_label_for_unknown_terms(): void
    {
        $company = Company::make(['payment_terms' => 'unknown']);
        $this->assertStringContainsString('immediat', strtolower($company->payment_terms_label));
    }

    // -------------------------------------------------------
    // Credit available
    // -------------------------------------------------------

        public function test_it_calculates_credit_available(): void
    {
        $company = Company::make(['credit_limit' => 1000.00, 'credit_used' => 250.00]);
        $this->assertEquals(750.00, $company->credit_available);
    }

        public function test_credit_available_never_goes_negative(): void
    {
        $company = Company::make(['credit_limit' => 100.00, 'credit_used' => 200.00]);
        $this->assertEquals(0.0, $company->credit_available);
    }

    // -------------------------------------------------------
    // hasAvailableCredit
    // -------------------------------------------------------

        public function test_has_available_credit_returns_true_when_no_credit_limit_set(): void
    {
        $company = Company::make(['credit_limit' => 0, 'credit_used' => 0]);
        $this->assertTrue($company->hasAvailableCredit(9999.00));
    }

        public function test_has_available_credit_returns_true_when_enough_credit(): void
    {
        $company = Company::make(['credit_limit' => 1000.00, 'credit_used' => 400.00]);
        $this->assertTrue($company->hasAvailableCredit(500.00));
    }

        public function test_has_available_credit_returns_false_when_insufficient(): void
    {
        $company = Company::make(['credit_limit' => 1000.00, 'credit_used' => 800.00]);
        $this->assertFalse($company->hasAvailableCredit(300.00));
    }

    // -------------------------------------------------------
    // needsApproval
    // -------------------------------------------------------

        public function test_needs_approval_returns_true_when_amount_exceeds_threshold(): void
    {
        $company = Company::make(['approval_threshold' => 500.00]);
        $this->assertTrue($company->needsApproval(600.00));
    }

        public function test_needs_approval_returns_false_when_amount_at_or_below_threshold(): void
    {
        $company = Company::make(['approval_threshold' => 500.00]);
        $this->assertFalse($company->needsApproval(500.00));
    }

        public function test_needs_approval_returns_false_when_no_threshold_set(): void
    {
        $company = Company::make(['approval_threshold' => null]);
        $this->assertFalse($company->needsApproval(99999.00));
    }

    // -------------------------------------------------------
    // getOwner
    // -------------------------------------------------------

        public function test_it_returns_owner_from_members(): void
    {
        $company = $this->makeCompany();
        $owner   = User::factory()->approved()->create([
            'company_id'   => $company->id,
            'company_role' => 'owner',
        ]);
        User::factory()->approved()->create([
            'company_id'   => $company->id,
            'company_role' => 'buyer',
        ]);

        $this->assertEquals($owner->id, $company->getOwner()->id);
    }

        public function test_get_owner_returns_null_when_no_owner(): void
    {
        $company = $this->makeCompany();
        $this->assertNull($company->getOwner());
    }
}
