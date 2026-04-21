<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPriceTier;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'tags'])
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('sku', 'like', "%$s%")
                    ->orWhere('brand', 'like', "%$s%")
                    ->orWhereJsonContains('name->ca', $s)
            )
            ->when(
                $request->category_id,
                fn($q, $c) => $q->where('category_id', $c)
            )
            ->when(
                $request->status === 'active',
                fn($q) => $q->where('is_active', true)
            )
            ->when(
                $request->status === 'inactive',
                fn($q) => $q->where('is_active', false)
            )
            ->when(
                $request->stock === 'low',
                fn($q) => $q->lowStock()
            )
            ->when(
                $request->stock === 'out',
                fn($q) => $q->outOfStock()
            )
            ->when(
                $request->tag,
                fn($q, $t) => $q->whereHas('tags', fn($q) => $q->where('slug', $t))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('sort_order')->get();
        $allTags    = ProductTag::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'allTags'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $allTags    = ProductTag::orderBy('name')->get();
        $clients    = User::approved()->orderBy('company_name')->get();

        return view('admin.products.create', compact('categories', 'allTags', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create([
                'category_id'         => $validated['category_id'],
                'sku'                 => $validated['sku'],
                'slug'                => Str::slug($validated['name_ca']),
                'brand'               => $validated['brand'],
                'price'               => $validated['price'],
                'vat_rate'            => $validated['vat_rate'],
                'stock'               => $validated['stock'],
                'min_order_quantity'  => $validated['min_order_quantity'],
                'unit'                => $validated['unit'],
                'is_active'           => $request->boolean('is_active', true),
                'is_featured'         => $request->boolean('is_featured', false),
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? null,
                'notify_low_stock'    => $request->boolean('notify_low_stock', false),
                'image'               => $request->hasFile('image')
                    ? $request->file('image')->store('products', 'public')
                    : null,
                'name' => [
                    'ca' => $validated['name_ca'],
                    'es' => $validated['name_es'],
                    'en' => $validated['name_en'] ?? $validated['name_es'],
                ],
                'short_description' => [
                    'ca' => $validated['short_description_ca'] ?? null,
                    'es' => $validated['short_description_es'] ?? null,
                    'en' => $validated['short_description_en'] ?? null,
                ],
                'description' => [
                    'ca' => $validated['description_ca'] ?? null,
                    'es' => $validated['description_es'] ?? null,
                    'en' => $validated['description_en'] ?? null,
                ],
                'meta_title' => [
                    'ca' => $validated['meta_title_ca'] ?? null,
                    'es' => $validated['meta_title_es'] ?? null,
                    'en' => $validated['meta_title_en'] ?? null,
                ],
                'meta_description' => [
                    'ca' => $validated['meta_description_ca'] ?? null,
                    'es' => $validated['meta_description_es'] ?? null,
                    'en' => $validated['meta_description_en'] ?? null,
                ],
                'meta_keywords' => [
                    'ca' => $validated['meta_keywords_ca'] ?? null,
                    'es' => $validated['meta_keywords_es'] ?? null,
                    'en' => $validated['meta_keywords_en'] ?? null,
                ],
            ]);

            $this->syncGallery($request, $product);
            $this->syncVariants($request, $product);
            $this->syncPriceTiers($request, $product);
            $this->syncTags($request, $product);
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Producte creat correctament.');
    }

    public function edit(int $id)
    {
        $product = Product::with([
            'images', 'variants', 'priceTiers.user', 'tags',
        ])->findOrFail($id);

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $allTags    = ProductTag::orderBy('name')->get();
        $clients    = User::approved()->orderBy('company_name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'allTags', 'clients'));
    }

    public function update(Request $request, int $id)
    {
        $product   = Product::findOrFail($id);
        $validated = $this->validateProduct($request, $id);

        DB::transaction(function () use ($request, $validated, $product) {
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                if ($imagePath) Storage::disk('public')->delete($imagePath);
                $imagePath = $request->file('image')->store('products', 'public');
            }
            if ($request->boolean('remove_image') && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            $product->update([
                'category_id'         => $validated['category_id'],
                'sku'                 => $validated['sku'],
                'slug'                => Str::slug($validated['name_ca']),
                'brand'               => $validated['brand'] ?? null,
                'price'               => $validated['price'],
                'vat_rate'            => $validated['vat_rate'],
                'stock'               => $validated['stock'],
                'min_order_quantity'  => $validated['min_order_quantity'],
                'unit'                => $validated['unit'],
                'is_active'           => $request->boolean('is_active'),
                'is_featured'         => $request->boolean('is_featured'),
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? null,
                'notify_low_stock'    => $request->boolean('notify_low_stock', false),
                'image'               => $imagePath,
                'name' => [
                    'ca' => $validated['name_ca'],
                    'es' => $validated['name_es'],
                    'en' => $validated['name_en'] ?? $validated['name_es'],
                ],
                'short_description' => [
                    'ca' => $validated['short_description_ca'] ?? null,
                    'es' => $validated['short_description_es'] ?? null,
                    'en' => $validated['short_description_en'] ?? null,
                ],
                'description' => [
                    'ca' => $validated['description_ca'] ?? null,
                    'es' => $validated['description_es'] ?? null,
                    'en' => $validated['description_en'] ?? null,
                ],
                'meta_title' => [
                    'ca' => $validated['meta_title_ca'] ?? null,
                    'es' => $validated['meta_title_es'] ?? null,
                    'en' => $validated['meta_title_en'] ?? null,
                ],
                'meta_description' => [
                    'ca' => $validated['meta_description_ca'] ?? null,
                    'es' => $validated['meta_description_es'] ?? null,
                    'en' => $validated['meta_description_en'] ?? null,
                ],
                'meta_keywords' => [
                    'ca' => $validated['meta_keywords_ca'] ?? null,
                    'es' => $validated['meta_keywords_es'] ?? null,
                    'en' => $validated['meta_keywords_en'] ?? null,
                ],
            ]);

            $this->syncGallery($request, $product);
            $this->syncVariants($request, $product);
            $this->syncPriceTiers($request, $product);
            $this->syncTags($request, $product);

            // Fire low-stock notification if threshold crossed
            if ($product->notify_low_stock && $product->is_low_stock) {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new LowStockNotification($product));
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Producte actualitzat correctament.');
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producte eliminat correctament.');
    }

    public function toggle(int $id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        return back()->with(
            'success',
            'Producte ' . ($product->is_active ? 'activat' : 'desactivat') . ' correctament.'
        );
    }

    /** DELETE a single gallery image via AJAX */
    public function destroyImage(int $imageId)
    {
        $image = ProductImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Private helpers
    |--------------------------------------------------------------------------
    */

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id'           => ['required', 'exists:categories,id'],
            'sku'                   => ['required', 'string', 'max:100', "unique:products,sku,{$ignoreId}"],
            'brand'                 => ['nullable', 'string', 'max:100'],
            'price'                 => ['required', 'numeric', 'min:0'],
            'vat_rate'              => ['required', 'numeric', 'min:0', 'max:100'],
            'stock'                 => ['required', 'integer', 'min:0'],
            'min_order_quantity'    => ['required', 'integer', 'min:1'],
            'unit'                  => ['required', 'string', 'max:50'],
            'is_active'             => ['boolean'],
            'is_featured'           => ['boolean'],
            'low_stock_threshold'   => ['nullable', 'integer', 'min:1'],
            'notify_low_stock'      => ['boolean'],
            'name_ca'               => ['required', 'string', 'max:255'],
            'name_es'               => ['required', 'string', 'max:255'],
            'name_en'               => ['nullable', 'string', 'max:255'],
            'short_description_ca'  => ['nullable', 'string', 'max:500'],
            'short_description_es'  => ['nullable', 'string', 'max:500'],
            'short_description_en'  => ['nullable', 'string', 'max:500'],
            'description_ca'        => ['nullable', 'string'],
            'description_es'        => ['nullable', 'string'],
            'description_en'        => ['nullable', 'string'],
            // SEO
            'meta_title_ca'         => ['nullable', 'string', 'max:70'],
            'meta_title_es'         => ['nullable', 'string', 'max:70'],
            'meta_title_en'         => ['nullable', 'string', 'max:70'],
            'meta_description_ca'   => ['nullable', 'string', 'max:160'],
            'meta_description_es'   => ['nullable', 'string', 'max:160'],
            'meta_description_en'   => ['nullable', 'string', 'max:160'],
            'meta_keywords_ca'      => ['nullable', 'string', 'max:255'],
            'meta_keywords_es'      => ['nullable', 'string', 'max:255'],
            'meta_keywords_en'      => ['nullable', 'string', 'max:255'],
            'image'                 => ['nullable', 'image', 'max:4096'],
            // Gallery
            'gallery.*'             => ['nullable', 'image', 'max:4096'],
            // Variants
            'variants.*.type'             => ['nullable', 'string', 'max:50'],
            'variants.*.value'            => ['nullable', 'string', 'max:100'],
            'variants.*.sku'              => ['nullable', 'string', 'max:100'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.stock'            => ['nullable', 'integer', 'min:0'],
            'variants.*.image'            => ['nullable', 'image', 'max:4096'],
            // Price tiers
            'tiers.*.user_id'       => ['nullable', 'exists:users,id'],
            'tiers.*.min_quantity'  => ['nullable', 'integer', 'min:1'],
            'tiers.*.price'         => ['nullable', 'numeric', 'min:0'],
            'tiers.*.label'         => ['nullable', 'string', 'max:100'],
            'tiers.*.valid_from'    => ['nullable', 'date'],
            'tiers.*.valid_until'   => ['nullable', 'date', 'after_or_equal:tiers.*.valid_from'],
            // Tags
            'tags'                  => ['nullable', 'string'],
        ]);
    }

    private function syncGallery(Request $request, Product $product): void
    {
        // Delete images marked for removal
        if ($request->has('delete_images')) {
            foreach ((array) $request->delete_images as $imgId) {
                $img = ProductImage::find($imgId);
                if ($img && $img->product_id === $product->id) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }
        }

        // Upload new gallery images
        if ($request->hasFile('gallery')) {
            $sort = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'path'       => $path,
                    'alt'        => $product->getTranslation('name', 'ca'),
                    'sort_order' => ++$sort,
                ]);
            }
        }
    }

    private function syncVariants(Request $request, Product $product): void
    {
        if (!$request->has('variants')) {
            return;
        }

        $submittedIds  = [];
        $variantFiles  = $request->file('variants') ?? [];

        foreach ($request->variants as $i => $data) {
            // Skip empty rows
            if (empty($data['type']) || empty($data['value'])) continue;

            $attrs = [
                'type'             => $data['type'],
                'value'            => $data['value'],
                'sku'              => $data['sku'] ?? null,
                'price_adjustment' => $data['price_adjustment'] ?? 0,
                'stock'            => $data['stock'] ?? 0,
                'is_active'        => isset($data['is_active']),
                'sort_order'       => $data['sort_order'] ?? 0,
            ];

            $imageFile = $variantFiles[$i]['image'] ?? null;

            if (!empty($data['id'])) {
                $variant = ProductVariant::find($data['id']);
                if ($variant && $variant->product_id === $product->id) {
                    // Remove image if checkbox was ticked
                    if (!empty($data['remove_image']) && $variant->image) {
                        Storage::disk('public')->delete($variant->image);
                        $attrs['image'] = null;
                    }
                    // Upload new image (replaces existing)
                    if ($imageFile) {
                        if ($variant->image) Storage::disk('public')->delete($variant->image);
                        $attrs['image'] = $imageFile->store('products/variants', 'public');
                    }
                    $variant->update($attrs);
                    $submittedIds[] = $variant->id;
                }
            } else {
                if ($imageFile) {
                    $attrs['image'] = $imageFile->store('products/variants', 'public');
                }
                $variant = $product->variants()->create($attrs);
                $submittedIds[] = $variant->id;
            }
        }

        // Remove variants not in the submission (delete their images too)
        $toDelete = $product->variants()->whereNotIn('id', $submittedIds)->get();
        foreach ($toDelete as $v) {
            if ($v->image) Storage::disk('public')->delete($v->image);
        }
        $product->variants()->whereNotIn('id', $submittedIds)->delete();
    }

    private function syncPriceTiers(Request $request, Product $product): void
    {
        if (!$request->has('tiers')) {
            return;
        }

        $submittedIds = [];

        foreach ($request->tiers as $data) {
            if (empty($data['price']) || empty($data['min_quantity'])) continue;

            $attrs = [
                'user_id'      => $data['user_id'] ?? null,
                'min_quantity' => $data['min_quantity'],
                'price'        => $data['price'],
                'label'        => $data['label'] ?? null,
                'valid_from'   => $data['valid_from'] ?? null,
                'valid_until'  => $data['valid_until'] ?? null,
                'is_active'    => isset($data['is_active']),
            ];

            if (!empty($data['id'])) {
                $tier = ProductPriceTier::find($data['id']);
                if ($tier && $tier->product_id === $product->id) {
                    $tier->update($attrs);
                    $submittedIds[] = $tier->id;
                }
            } else {
                $tier = $product->priceTiers()->create($attrs);
                $submittedIds[] = $tier->id;
            }
        }

        $product->priceTiers()
            ->whereNotIn('id', $submittedIds)
            ->delete();
    }

    private function syncTags(Request $request, Product $product): void
    {
        $raw = trim($request->input('tags', ''));

        if ($raw === '') {
            $product->tags()->detach();
            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $raw)));
        $tagIds   = collect($tagNames)
            ->map(fn($name) => ProductTag::findOrCreateByName($name)->id)
            ->toArray();

        $product->tags()->sync($tagIds);
    }
}
