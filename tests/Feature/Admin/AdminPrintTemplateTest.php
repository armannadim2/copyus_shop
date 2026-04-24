<?php

namespace Tests\Feature\Admin;

use App\Models\PrintTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrintTemplateTest extends TestCase
{
    use RefreshDatabase;

    private function makeTemplate(array $overrides = []): PrintTemplate
    {
        return PrintTemplate::create(array_merge([
            'slug'                 => 'template-' . uniqid(),
            'name'                 => ['ca' => 'Test', 'es' => 'Test', 'en' => 'Test'],
            'base_price'           => 10.00,
            'vat_rate'             => 21.00,
            'base_production_days' => 3,
            'is_active'            => true,
        ], $overrides));
    }

    private function templatePayload(array $overrides = []): array
    {
        return array_merge([
            'name_ca'              => 'Plantilla de prova ' . uniqid(),
            'name_es'              => 'Plantilla de prueba',
            'name_en'              => 'Test template',
            'base_price'           => 12.50,
            'vat_rate'             => 21.00,
            'base_production_days' => 3,
            'sort_order'           => 0,
            'is_active'            => true,
        ], $overrides);
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_admin_print_templates(): void
    {
        $this->get(route('admin.print.templates.index'))
             ->assertRedirect(route('login'));
    }

        public function test_b2b_user_cannot_access_admin_print_templates(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.print.templates.index'))
             ->assertForbidden();
    }

        public function test_admin_can_view_print_templates_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.print.templates.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Create Template
    // -------------------------------------------------------

        public function test_admin_can_view_create_template_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.print.templates.create'))
             ->assertOk();
    }

        public function test_admin_can_create_a_print_template(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->post(route('admin.print.templates.store'), $this->templatePayload())
             ->assertRedirect();

        $this->assertDatabaseHas('print_templates', [
            'base_price' => 12.50,
        ]);
    }

        public function test_template_creation_requires_name_and_base_price(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->post(route('admin.print.templates.store'), [])
             ->assertSessionHasErrors(['name_ca', 'name_es', 'base_price']);
    }

    // -------------------------------------------------------
    // Edit / Update Template
    // -------------------------------------------------------

        public function test_admin_can_view_edit_template_form(): void
    {
        $admin    = User::factory()->admin()->create();
        $template = $this->makeTemplate();

        $this->actingAs($admin)
             ->get(route('admin.print.templates.edit', $template))
             ->assertOk();
    }

        public function test_admin_can_update_a_print_template(): void
    {
        $admin    = User::factory()->admin()->create();
        $template = $this->makeTemplate();

        $this->actingAs($admin)
             ->put(route('admin.print.templates.update', $template), $this->templatePayload([
                 'base_price' => 20.00,
             ]))->assertRedirect();

        $this->assertDatabaseHas('print_templates', [
            'id'         => $template->id,
            'base_price' => 20.00,
        ]);
    }

    // -------------------------------------------------------
    // Delete Template
    // -------------------------------------------------------

        public function test_admin_can_delete_a_print_template(): void
    {
        $admin    = User::factory()->admin()->create();
        $template = $this->makeTemplate();

        $this->actingAs($admin)
             ->delete(route('admin.print.templates.destroy', $template))
             ->assertRedirect();

        $this->assertDatabaseMissing('print_templates', ['id' => $template->id]);
    }

    // -------------------------------------------------------
    // Toggle Active Status
    // -------------------------------------------------------

        public function test_admin_can_toggle_template_active_status(): void
    {
        $admin    = User::factory()->admin()->create();
        $template = $this->makeTemplate(['is_active' => true]);

        $this->actingAs($admin)
             ->patch(route('admin.print.templates.toggle', $template))
             ->assertRedirect();

        $this->assertDatabaseHas('print_templates', [
            'id'        => $template->id,
            'is_active' => false,
        ]);
    }
}
