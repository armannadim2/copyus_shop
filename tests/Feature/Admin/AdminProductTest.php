<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(int $categoryId): array
    {
        return [
            'category_id'       => $categoryId,
            'sku'               => 'TEST-0001',
            'brand'             => 'TestBrand',
            'name_ca'           => 'Producte test',
            'name_es'           => 'Producto test',
            'name_en'           => 'Test product',
            'short_desc_ca'     => 'Descripció curta',
            'short_desc_es'     => 'Descripción corta',
            'short_desc_en'     => 'Short description',
            'description_ca'    => 'Descripció llarga',
            'description_es'    => 'Descripción larga',
            'description_en'    => 'Long description',
            'price'             => 49.99,
            'vat_rate'          => 21.00,
            'stock'             => 100,
            'min_order_quantity' => 1,
            'unit'              => 'unit',
            'is_active'         => true,
            'is_featured'       => false,
        ];
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_admin_can_access_product_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertViewIs('admin.products.index');
    }

        public function test_b2b_user_cannot_access_admin_products(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Create Product
    // -------------------------------------------------------

        public function test_admin_can_view_create_product_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertViewIs('admin.products.create');
    }

        public function test_admin_can_create_a_product(): void
    {
        Storage::fake('public');

        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->validPayload($category->id))
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'sku'   => 'TEST-0001',
            'brand' => 'TestBrand',
        ]);
    }

        public function test_create_product_fails_without_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [])
            ->assertSessionHasErrors([
                'sku',
                'brand',
                'price',
                'category_id',
            ]);
    }

        public function test_create_product_fails_with_duplicate_sku(): void
    {
        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Product::factory()->create(['sku' => 'DUPE-001']);

        $payload        = $this->validPayload($category->id);
        $payload['sku'] = 'DUPE-001';

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $payload)
            ->assertSessionHasErrors(['sku']);
    }

        public function test_admin_can_upload_product_image(): void
    {
        Storage::fake('public');

        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $payload          = $this->validPayload($category->id);
        $payload['image'] = UploadedFile::fake()->image('product.jpg', 800, 800);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $payload)
            ->assertRedirect();

        $product = Product::where('sku', 'TEST-0001')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    // -------------------------------------------------------
    // Edit Product
    // -------------------------------------------------------

        public function test_admin_can_view_edit_product_form(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product->id))
            ->assertOk()
            ->assertViewIs('admin.products.edit');
    }

        public function test_admin_can_update_a_product(): void
    {
        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $product  = Product::factory()->create(['category_id' => $category->id]);

        $payload          = $this->validPayload($category->id);
        $payload['sku']   = $product->sku;
        $payload['brand'] = 'UpdatedBrand';

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product->id), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'brand' => 'UpdatedBrand',
        ]);
    }

    // -------------------------------------------------------
    // Toggle Active / Delete
    // -------------------------------------------------------

        public function test_admin_can_toggle_product_active_status(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.products.toggle', $product->id))
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id'        => $product->id,
            'is_active' => false,
        ]);
    }

        public function test_admin_can_delete_a_product(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product->id))
            ->assertRedirect();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
