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

    /*
    |--------------------------------------------------------------------------
    | Bulk CSV Import
    |--------------------------------------------------------------------------
    */

    public function bulkUpload()
    {
        $columns = $this->csvColumns();

        return view('admin.products.bulk-upload', compact('columns'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'images'   => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:8192'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'No s\'ha pogut llegir el fitxer CSV.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return back()->with('error', 'El fitxer CSV està buit.');
        }
        $header = array_map(fn($h) => trim((string) $h), $header);

        $required = ['sku', 'category_slug', 'brand', 'name_ca', 'name_es', 'price', 'vat_rate', 'stock', 'min_order_quantity', 'unit'];
        $missing  = array_diff($required, $header);
        if (! empty($missing)) {
            fclose($handle);
            return back()->with('error', 'Falten columnes obligatòries: ' . implode(', ', $missing));
        }

        $created = 0;
        $updated = 0;
        $errors  = [];
        $row     = 1;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                if (count(array_filter($data, fn($v) => $v !== '' && $v !== null)) === 0) {
                    continue;
                }

                $values = array_combine($header, array_pad($data, count($header), null));

                $category = Category::where('slug', trim((string) $values['category_slug']))->first();
                if (! $category) {
                    $errors[] = "Fila $row: categoria «{$values['category_slug']}» no trobada.";
                    continue;
                }

                $sku = trim((string) $values['sku']);
                if ($sku === '') {
                    $errors[] = "Fila $row: SKU buit.";
                    continue;
                }

                $attributes = [
                    'category_id'         => $category->id,
                    'brand'               => trim((string) $values['brand']),
                    'slug'                => Str::slug($values['name_ca']),
                    'price'               => (float) $values['price'],
                    'vat_rate'            => (float) $values['vat_rate'],
                    'stock'               => (int) $values['stock'],
                    'min_order_quantity'  => (int) $values['min_order_quantity'],
                    'unit'                => trim((string) $values['unit']),
                    'is_active'           => $this->csvBool($values['is_active'] ?? '1'),
                    'is_featured'         => $this->csvBool($values['is_featured'] ?? '0'),
                    'low_stock_threshold' => $this->csvNullableInt($values['low_stock_threshold'] ?? null),
                    'name' => [
                        'ca' => $values['name_ca'],
                        'es' => $values['name_es'],
                        'en' => $values['name_en'] ?? $values['name_es'],
                    ],
                    'short_description' => [
                        'ca' => $values['short_description_ca'] ?? null,
                        'es' => $values['short_description_es'] ?? null,
                        'en' => $values['short_description_en'] ?? null,
                    ],
                    'description' => [
                        'ca' => $values['description_ca'] ?? null,
                        'es' => $values['description_es'] ?? null,
                        'en' => $values['description_en'] ?? null,
                    ],
                ];

                $existing = Product::where('sku', $sku)->first();
                if ($existing) {
                    $existing->update($attributes);
                    $updated++;
                } else {
                    Product::create(array_merge(['sku' => $sku], $attributes));
                    $created++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Error durant la importació: ' . $e->getMessage());
        }

        fclose($handle);

        $imageReport = $this->matchAndAttachImages($request->file('images') ?? []);

        $summary = "Importació completada: {$created} creats, {$updated} actualitzats.";
        if ($imageReport['matched'] > 0 || $imageReport['gallery_added'] > 0) {
            $summary .= " Imatges: {$imageReport['matched']} principals, {$imageReport['gallery_added']} galeria.";
        }
        if (! empty($imageReport['unmatched'])) {
            $summary .= ' Imatges sense coincidència: ' . count($imageReport['unmatched']) . '.';
        }
        if (! empty($errors)) {
            $summary .= ' Errors: ' . count($errors);
            return back()
                ->with('warning', $summary)
                ->with('csv_errors', $errors)
                ->with('unmatched_images', $imageReport['unmatched']);
        }

        return redirect()->route('admin.products.index')
            ->with('success', $summary . ' ✅')
            ->with('unmatched_images', $imageReport['unmatched']);
    }

    /*
    |--------------------------------------------------------------------------
    | Standalone Bulk Image Upload — match files to existing products by SKU
    |--------------------------------------------------------------------------
    */

    public function bulkImages()
    {
        return view('admin.products.bulk-images');
    }

    public function bulkImagesStore(Request $request)
    {
        $request->validate([
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => ['file', 'image', 'max:8192'],
        ]);

        $report = $this->matchAndAttachImages($request->file('images'));

        $msg = "Imatges processades: {$report['matched']} principals, {$report['gallery_added']} galeria.";
        if (! empty($report['unmatched'])) {
            $msg .= ' Sense coincidència: ' . count($report['unmatched']) . '.';
            return back()
                ->with('warning', $msg)
                ->with('unmatched_images', $report['unmatched']);
        }

        return redirect()->route('admin.products.index')->with('success', $msg . ' ✅');
    }

    public function sampleCsv()
    {
        $columns = $this->csvColumns();

        $sampleRows = [
            [
                'sku'                  => 'SAMPLE-001',
                'category_slug'        => 'paper',
                'brand'                => 'Acme',
                'name_ca'              => 'Paper A4 80g',
                'name_es'              => 'Papel A4 80g',
                'name_en'              => 'A4 paper 80g',
                'short_description_ca' => 'Paquet de 500 fulls',
                'short_description_es' => 'Paquete de 500 hojas',
                'short_description_en' => '500-sheet pack',
                'description_ca'       => 'Paper blanc d\'alta qualitat per impressió làser i tinta.',
                'description_es'       => 'Papel blanco de alta calidad para impresión láser y tinta.',
                'description_en'       => 'High-quality white paper for laser and inkjet printing.',
                'price'                => '4.50',
                'vat_rate'             => '21.00',
                'stock'                => '500',
                'min_order_quantity'   => '1',
                'unit'                 => 'unit',
                'is_active'            => '1',
                'is_featured'          => '0',
                'low_stock_threshold'  => '50',
            ],
            [
                'sku'                  => 'SAMPLE-002',
                'category_slug'        => 'tinta',
                'brand'                => 'Generic',
                'name_ca'              => 'Cartutx tinta negra',
                'name_es'              => 'Cartucho tinta negra',
                'name_en'              => 'Black ink cartridge',
                'short_description_ca' => 'Compatible amb impressores estàndard',
                'short_description_es' => 'Compatible con impresoras estándar',
                'short_description_en' => 'Compatible with standard printers',
                'description_ca'       => '',
                'description_es'       => '',
                'description_en'       => '',
                'price'                => '12.99',
                'vat_rate'             => '21.00',
                'stock'                => '120',
                'min_order_quantity'   => '1',
                'unit'                 => 'unit',
                'is_active'            => '1',
                'is_featured'          => '1',
                'low_stock_threshold'  => '10',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $columns);
        foreach ($sampleRows as $row) {
            $line = [];
            foreach ($columns as $col) {
                $line[] = $row[$col] ?? '';
            }
            fputcsv($output, $line);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products-sample.csv"',
        ]);
    }

    private function csvColumns(): array
    {
        return [
            'sku', 'category_slug', 'brand',
            'name_ca', 'name_es', 'name_en',
            'short_description_ca', 'short_description_es', 'short_description_en',
            'description_ca', 'description_es', 'description_en',
            'price', 'vat_rate', 'stock', 'min_order_quantity', 'unit',
            'is_active', 'is_featured', 'low_stock_threshold',
        ];
    }

    private function csvBool($v): bool
    {
        $v = strtolower(trim((string) $v));
        return in_array($v, ['1', 'true', 'yes', 'si', 'sí', 'y'], true);
    }

    private function csvNullableInt($v): ?int
    {
        $v = trim((string) $v);
        return $v === '' ? null : (int) $v;
    }

    /**
     * Match uploaded image files to products by SKU embedded in the filename.
     *
     *  - {SKU}.{ext}        → main image (replaces existing)
     *  - {SKU}-N.{ext}      → gallery image (appended; sort_order = N)
     *
     * SKU matching is case-insensitive. The full basename is tried first so
     * SKUs containing dashes still resolve correctly before falling back to
     * the {SKU}-N pattern.
     *
     * @param  \Illuminate\Http\UploadedFile[]  $files
     * @return array{matched:int, gallery_added:int, unmatched:array<string>}
     */
    private function matchAndAttachImages(array $files): array
    {
        $matched       = 0;
        $galleryAdded  = 0;
        $unmatched     = [];

        if (empty($files)) {
            return ['matched' => 0, 'gallery_added' => 0, 'unmatched' => []];
        }

        // Build a case-insensitive SKU → product map once
        $skuMap = Product::pluck('id', 'sku')
            ->mapWithKeys(fn($id, $sku) => [strtolower($sku) => ['id' => $id, 'sku' => $sku]]);

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $original = $file->getClientOriginalName();
            $basename = pathinfo($original, PATHINFO_FILENAME);
            $key      = strtolower($basename);

            // 1. Exact SKU match → main image
            if ($skuMap->has($key)) {
                $entry   = $skuMap->get($key);
                $product = Product::find($entry['id']);
                if ($product) {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $product->update([
                        'image' => $file->store('products', 'public'),
                    ]);
                    $matched++;
                    continue;
                }
            }

            // 2. {SKU}-N pattern → gallery image
            if (preg_match('/^(.+)-(\d+)$/', $basename, $m)) {
                $skuKey  = strtolower($m[1]);
                $sortIdx = (int) $m[2];
                if ($skuMap->has($skuKey)) {
                    $entry   = $skuMap->get($skuKey);
                    $product = Product::with('images')->find($entry['id']);
                    if ($product) {
                        $path = $file->store('products/gallery', 'public');
                        $product->images()->create([
                            'path'       => $path,
                            'alt'        => $product->getTranslation('name', 'ca', false) ?: $product->sku,
                            'sort_order' => $sortIdx,
                        ]);
                        $galleryAdded++;
                        continue;
                    }
                }
            }

            $unmatched[] = $original;
        }

        return [
            'matched'       => $matched,
            'gallery_added' => $galleryAdded,
            'unmatched'     => $unmatched,
        ];
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
            'brand'                 => ['required', 'string', 'max:100'],
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
