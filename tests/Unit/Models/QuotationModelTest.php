<?php

namespace Tests\Unit\Models;

use App\Models\Quotation;
use Tests\TestCase;

class QuotationModelTest extends TestCase
{
        public function test_it_detects_expired_quotation(): void
    {
        $expired = Quotation::factory()->expired()->make();
        $this->assertTrue($expired->is_expired);
    }

        public function test_it_detects_acceptable_quotation(): void
    {
        $quoted = Quotation::factory()->quoted()->make();
        $this->assertTrue($quoted->is_acceptable);
    }

        public function test_it_detects_non_acceptable_when_not_quoted(): void
    {
        $pending = Quotation::factory()->make(['status' => 'pending']);
        $this->assertFalse($pending->is_acceptable);
    }

        public function test_it_returns_correct_status_colors(): void
    {
        $statuses = [
            'pending'   => 'bg-yellow-50 text-yellow-700',
            'quoted'    => 'bg-purple-50 text-purple-700',
            'accepted'  => 'bg-green-50 text-green-700',
            'rejected'  => 'bg-red-50 text-red-600',
        ];

        foreach ($statuses as $status => $expected) {
            $quotation = Quotation::factory()->make(['status' => $status]);
            $this->assertEquals($expected, $quotation->status_color);
        }
    }

        public function test_it_scopes_active_quotations(): void
    {
        Quotation::factory()->count(2)->create(['status' => 'pending']);
        Quotation::factory()->count(1)->create(['status' => 'reviewing']);
        Quotation::factory()->count(3)->create(['status' => 'accepted']);

        $this->assertCount(3, Quotation::active()->get());
    }
}
