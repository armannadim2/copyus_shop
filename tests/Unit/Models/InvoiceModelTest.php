<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use Tests\TestCase;

class InvoiceModelTest extends TestCase
{
        public function test_it_detects_paid_invoice(): void
    {
        $paid   = Invoice::factory()->paid()->make();
        $unpaid = Invoice::factory()->make();

        $this->assertTrue($paid->is_paid);
        $this->assertFalse($unpaid->is_paid);
    }

        public function test_it_detects_overdue_invoice(): void
    {
        $overdue = Invoice::factory()->overdue()->make();
        $current = Invoice::factory()->make();

        $this->assertTrue($overdue->is_overdue);
        $this->assertFalse($current->is_overdue);
    }

        public function test_it_returns_correct_status_colors(): void
    {
        $issued    = Invoice::factory()->make(['status' => 'issued']);
        $paid      = Invoice::factory()->paid()->make();
        $cancelled = Invoice::factory()->make(['status' => 'cancelled']);

        $this->assertStringContainsString('blue', $issued->status_color);
        $this->assertStringContainsString('green', $paid->status_color);
        $this->assertStringContainsString('gray', $cancelled->status_color);
    }

        public function test_it_scopes_issued_invoices(): void
    {
        Invoice::factory()->count(3)->create(['status' => 'issued']);
        Invoice::factory()->count(2)->paid()->create();

        $this->assertCount(3, Invoice::issued()->get());
    }

        public function test_it_scopes_paid_invoices(): void
    {
        Invoice::factory()->count(2)->paid()->create();
        Invoice::factory()->count(3)->create(['status' => 'issued']);

        $this->assertCount(2, Invoice::paid()->get());
    }

        public function test_it_scopes_overdue_invoices(): void
    {
        Invoice::factory()->count(2)->overdue()->create();
        Invoice::factory()->count(3)->create();

        $this->assertCount(2, Invoice::overdue()->get());
    }
}
