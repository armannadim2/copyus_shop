<?php

namespace Tests\Unit\Services;

use App\Models\PrintOption;
use App\Models\PrintOptionValue;
use App\Models\PrintQuantityTier;
use App\Models\PrintTemplate;
use App\Services\PrintPriceCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintPriceCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private PrintPriceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PrintPriceCalculator();
    }

    private function makeTemplate(float $basePrice = 10.00, int $baseDays = 3): PrintTemplate
    {
        return PrintTemplate::create([
            'slug'                 => 'test-template-' . uniqid(),
            'name'                 => ['ca' => 'Test', 'es' => 'Test', 'en' => 'Test'],
            'base_price'           => $basePrice,
            'vat_rate'             => 21.00,
            'base_production_days' => $baseDays,
            'is_active'            => true,
        ]);
    }

    private function addOption(PrintTemplate $template, string $key, bool $required = true): PrintOption
    {
        return PrintOption::create([
            'print_template_id' => $template->id,
            'key'               => $key,
            'label'             => ['ca' => $key, 'es' => $key, 'en' => $key],
            'is_required'       => $required,
        ]);
    }

    private function addValue(PrintOption $option, string $valueKey, float $modifier = 0, string $type = 'flat', int $daysMod = 0): PrintOptionValue
    {
        return PrintOptionValue::create([
            'print_option_id'       => $option->id,
            'value_key'             => $valueKey,
            'label'                 => ['ca' => $valueKey, 'es' => $valueKey, 'en' => $valueKey],
            'price_modifier'        => $modifier,
            'price_modifier_type'   => $type,
            'production_days_modifier' => $daysMod,
            'is_active'             => true,
        ]);
    }

    // -------------------------------------------------------
    // Base price only
    // -------------------------------------------------------

        public function test_it_returns_base_price_when_no_options_selected(): void
    {
        $template = $this->makeTemplate(10.00);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, [], 1);

        $this->assertEquals(10.00, $result->unitPrice);
        $this->assertEquals(10.00, $result->totalPrice);
    }

        public function test_it_multiplies_unit_price_by_quantity(): void
    {
        $template = $this->makeTemplate(10.00);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, [], 5);

        $this->assertEquals(10.00, $result->unitPrice);
        $this->assertEquals(50.00, $result->totalPrice);
    }

    // -------------------------------------------------------
    // Flat option modifiers
    // -------------------------------------------------------

        public function test_it_adds_flat_price_modifier_to_unit_price(): void
    {
        $template = $this->makeTemplate(10.00);
        $option   = $this->addOption($template, 'size');
        $this->addValue($option, 'a3', 5.00, 'flat');
        $template->load('options.values');

        $result = $this->calculator->calculate($template, ['size' => 'a3'], 1);

        $this->assertEquals(15.00, $result->unitPrice);
    }

        public function test_it_ignores_unknown_option_keys(): void
    {
        $template = $this->makeTemplate(10.00);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, ['nonexistent' => 'value'], 1);

        $this->assertEquals(10.00, $result->unitPrice);
    }

    // -------------------------------------------------------
    // Percent option modifiers
    // -------------------------------------------------------

        public function test_it_applies_percent_modifier_to_base_price(): void
    {
        $template = $this->makeTemplate(100.00);
        $option   = $this->addOption($template, 'finish');
        $this->addValue($option, 'glossy', 10.00, 'percent');
        $template->load('options.values');

        $result = $this->calculator->calculate($template, ['finish' => 'glossy'], 1);

        $this->assertEquals(110.00, $result->unitPrice);
    }

    // -------------------------------------------------------
    // Production days
    // -------------------------------------------------------

        public function test_it_adds_production_days_from_option_values(): void
    {
        $template = $this->makeTemplate(10.00, 3);
        $option   = $this->addOption($template, 'urgency');
        $this->addValue($option, 'express', 0, 'flat', -1);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, ['urgency' => 'express'], 1);

        $this->assertEquals(2, $result->productionDays);
    }

        public function test_production_days_never_go_below_one(): void
    {
        $template = $this->makeTemplate(10.00, 1);
        $option   = $this->addOption($template, 'speed');
        $this->addValue($option, 'turbo', 0, 'flat', -99);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, ['speed' => 'turbo'], 1);

        $this->assertEquals(1, $result->productionDays);
    }

    // -------------------------------------------------------
    // Quantity tier discounts
    // -------------------------------------------------------

        public function test_it_applies_quantity_tier_discount(): void
    {
        $template = $this->makeTemplate(100.00);
        PrintQuantityTier::create([
            'print_template_id' => $template->id,
            'min_quantity'      => 10,
            'discount_percent'  => 10.00,
            'is_active'         => true,
        ]);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, [], 10);

        $this->assertEquals(90.00, $result->unitPrice);
        $this->assertEquals(900.00, $result->totalPrice);
        $this->assertEquals(10.00, $result->tierDiscountPercent);
    }

        public function test_it_uses_best_matching_tier_for_quantity(): void
    {
        $template = $this->makeTemplate(100.00);
        PrintQuantityTier::create([
            'print_template_id' => $template->id,
            'min_quantity'      => 10,
            'discount_percent'  => 5.00,
            'is_active'         => true,
        ]);
        PrintQuantityTier::create([
            'print_template_id' => $template->id,
            'min_quantity'      => 50,
            'discount_percent'  => 15.00,
            'is_active'         => true,
        ]);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, [], 50);

        $this->assertEquals(15.00, $result->tierDiscountPercent);
    }

        public function test_no_tier_discount_when_quantity_below_threshold(): void
    {
        $template = $this->makeTemplate(100.00);
        PrintQuantityTier::create([
            'print_template_id' => $template->id,
            'min_quantity'      => 10,
            'discount_percent'  => 10.00,
            'is_active'         => true,
        ]);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, [], 5);

        $this->assertEquals(0.00, $result->tierDiscountPercent);
        $this->assertEquals(100.00, $result->unitPrice);
    }

    // -------------------------------------------------------
    // Validate
    // -------------------------------------------------------

        public function test_validate_returns_empty_array_for_valid_configuration(): void
    {
        $template = $this->makeTemplate();
        $option   = $this->addOption($template, 'size', true);
        $this->addValue($option, 'a4');
        $template->load('options.values');

        $errors = $this->calculator->validate($template, ['size' => 'a4']);

        $this->assertEmpty($errors);
    }

        public function test_validate_returns_error_for_missing_required_option(): void
    {
        $template = $this->makeTemplate();
        $this->addOption($template, 'size', true);
        $template->load('options.values');

        $errors = $this->calculator->validate($template, []);

        $this->assertNotEmpty($errors);
    }

        public function test_validate_does_not_require_optional_options(): void
    {
        $template = $this->makeTemplate();
        $option   = $this->addOption($template, 'coating', false);
        $this->addValue($option, 'none');
        $template->load('options.values');

        $errors = $this->calculator->validate($template, []);

        $this->assertEmpty($errors);
    }

        public function test_validate_returns_error_for_invalid_value_key(): void
    {
        $template = $this->makeTemplate();
        $option   = $this->addOption($template, 'size', true);
        $this->addValue($option, 'a4');
        $template->load('options.values');

        $errors = $this->calculator->validate($template, ['size' => 'nonexistent_key']);

        $this->assertNotEmpty($errors);
    }

    // -------------------------------------------------------
    // Breakdown
    // -------------------------------------------------------

        public function test_result_breakdown_contains_base_price_entry(): void
    {
        $template = $this->makeTemplate(10.00);
        $template->load('options.values');

        $result = $this->calculator->calculate($template, [], 1);

        $this->assertNotEmpty($result->breakdown);
        $this->assertEquals(10.00, $result->breakdown[0]['delta']);
    }
}
