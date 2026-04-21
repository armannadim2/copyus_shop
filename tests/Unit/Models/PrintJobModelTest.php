<?php

namespace Tests\Unit\Models;

use App\Models\PrintJob;
use Tests\TestCase;

class PrintJobModelTest extends TestCase
{
    private function makeJob(string $status): PrintJob
    {
        return new PrintJob(['status' => $status]);
    }

    // -------------------------------------------------------
    // is_editable
    // -------------------------------------------------------

        public function test_draft_job_is_editable(): void
    {
        $this->assertTrue($this->makeJob('draft')->is_editable);
    }

        public function test_in_cart_job_is_editable(): void
    {
        $this->assertTrue($this->makeJob('in_cart')->is_editable);
    }

        public function test_ordered_job_is_not_editable(): void
    {
        $this->assertFalse($this->makeJob('ordered')->is_editable);
    }

        public function test_in_production_job_is_not_editable(): void
    {
        $this->assertFalse($this->makeJob('in_production')->is_editable);
    }

        public function test_completed_job_is_not_editable(): void
    {
        $this->assertFalse($this->makeJob('completed')->is_editable);
    }

        public function test_cancelled_job_is_not_editable(): void
    {
        $this->assertFalse($this->makeJob('cancelled')->is_editable);
    }

    // -------------------------------------------------------
    // status_color
    // -------------------------------------------------------

        public function test_it_returns_correct_status_colors(): void
    {
        $cases = [
            'draft'         => 'gray',
            'in_cart'       => 'blue',
            'ordered'       => 'yellow',
            'in_production' => 'orange',
            'completed'     => 'green',
            'cancelled'     => 'red',
        ];

        foreach ($cases as $status => $expectedColor) {
            $this->assertEquals(
                $expectedColor,
                $this->makeJob($status)->status_color,
                "Failed for status: {$status}"
            );
        }
    }

        public function test_unknown_status_returns_gray_color(): void
    {
        $this->assertEquals('gray', $this->makeJob('mystery_status')->status_color);
    }
}
