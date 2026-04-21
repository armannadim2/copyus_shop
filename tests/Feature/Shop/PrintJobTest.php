<?php

namespace Tests\Feature\Shop;

use App\Models\PrintOption;
use App\Models\PrintOptionValue;
use App\Models\PrintTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeTemplate(array $overrides = []): PrintTemplate
    {
        return PrintTemplate::create(array_merge([
            'slug'                 => 'flyers-' . uniqid(),
            'name'                 => ['ca' => 'Flyers', 'es' => 'Flyers', 'en' => 'Flyers'],
            'base_price'           => 10.00,
            'vat_rate'             => 21.00,
            'base_production_days' => 3,
            'is_active'            => true,
        ], $overrides));
    }

    private function addOptionWithValues(PrintTemplate $template, string $key): PrintOption
    {
        $option = PrintOption::create([
            'print_template_id' => $template->id,
            'key'               => $key,
            'label'             => ['ca' => $key, 'es' => $key, 'en' => $key],
            'is_required'       => false,
        ]);

        PrintOptionValue::create([
            'print_option_id'       => $option->id,
            'value_key'             => 'standard',
            'label'                 => ['ca' => 'Standard', 'es' => 'Standard', 'en' => 'Standard'],
            'price_modifier'        => 0,
            'price_modifier_type'   => 'flat',
            'production_days_modifier' => 0,
            'is_active'             => true,
        ]);

        return $option;
    }

    // -------------------------------------------------------
    // Access Control — Gallery
    // -------------------------------------------------------

        public function test_guest_cannot_access_print_gallery(): void
    {
        $this->get(route('print.index'))
             ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_access_print_gallery(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
             ->get(route('print.index'))
             ->assertForbidden();
    }

        public function test_approved_user_can_view_print_gallery(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('print.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Template Builder
    // -------------------------------------------------------

        public function test_approved_user_can_access_print_builder_for_active_template(): void
    {
        $user     = User::factory()->approved()->create();
        $template = $this->makeTemplate(['is_active' => true]);

        $this->actingAs($user)
             ->get(route('print.builder', $template->slug))
             ->assertOk();
    }

        public function test_builder_returns_404_for_inactive_template(): void
    {
        $user     = User::factory()->approved()->create();
        $template = $this->makeTemplate(['is_active' => false]);

        $this->actingAs($user)
             ->get(route('print.builder', $template->slug))
             ->assertNotFound();
    }

    // -------------------------------------------------------
    // AJAX Price Calculation
    // -------------------------------------------------------

        public function test_calculate_endpoint_returns_json_with_price(): void
    {
        $user     = User::factory()->approved()->create();
        $template = $this->makeTemplate();

        $response = $this->actingAs($user)
             ->postJson(route('print.calculate', $template->slug), [
                 'quantity'      => 100,
                 'configuration' => [],
             ]);

        $response->assertOk()
                 ->assertJsonStructure(['unit_price', 'total_price', 'production_days']);
    }

        public function test_calculate_requires_quantity(): void
    {
        $user     = User::factory()->approved()->create();
        $template = $this->makeTemplate();

        $this->actingAs($user)
             ->postJson(route('print.calculate', $template->slug), [
                 'configuration' => [],
             ])->assertStatus(422);
    }

    // -------------------------------------------------------
    // Add Print Job to Cart
    // -------------------------------------------------------

        public function test_approved_user_can_add_print_job_to_cart(): void
    {
        $user     = User::factory()->approved()->create();
        $template = $this->makeTemplate();

        $this->actingAs($user)
             ->post(route('print.add-to-cart', $template->slug), [
                 'quantity'      => 50,
                 'configuration' => [],
             ])->assertRedirect();

        $this->assertDatabaseHas('print_jobs', [
            'user_id'           => $user->id,
            'print_template_id' => $template->id,
            'quantity'          => 50,
        ]);
    }

        public function test_add_to_cart_requires_positive_quantity(): void
    {
        $user     = User::factory()->approved()->create();
        $template = $this->makeTemplate();

        $this->actingAs($user)
             ->post(route('print.add-to-cart', $template->slug), [
                 'quantity' => 0,
             ])->assertSessionHasErrors('quantity');
    }

    // -------------------------------------------------------
    // My Jobs
    // -------------------------------------------------------

        public function test_approved_user_can_view_their_print_jobs(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('print.my-jobs'))
             ->assertOk();
    }

        public function test_guest_cannot_view_print_jobs(): void
    {
        $this->get(route('print.my-jobs'))
             ->assertRedirect(route('login'));
    }
}
