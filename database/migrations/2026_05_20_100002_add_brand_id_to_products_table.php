<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing brand strings into the brands table
        $existingBrands = DB::table('products')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand');

        $brandMap = [];
        foreach ($existingBrands as $brandName) {
            $slug = Str::slug($brandName);
            $base = $slug;
            $i    = 1;
            while (DB::table('brands')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $id = DB::table('brands')->insertGetId([
                'name'        => json_encode(['ca' => $brandName, 'es' => $brandName, 'en' => $brandName]),
                'description' => null,
                'slug'        => $slug,
                'image'       => null,
                'is_active'   => true,
                'sort_order'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $brandMap[$brandName] = $id;
        }

        // Add brand_id column
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
        });

        // Populate brand_id from existing brand strings
        foreach ($brandMap as $brandName => $brandId) {
            DB::table('products')
                ->where('brand', $brandName)
                ->update(['brand_id' => $brandId]);
        }

        // Drop the old brand string column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('category_id');
        });

        // Restore brand names from brand_id
        $brands = DB::table('brands')->get()->keyBy('id');
        DB::table('products')->whereNotNull('brand_id')->get()->each(function ($product) use ($brands) {
            if (isset($brands[$product->brand_id])) {
                $name = json_decode($brands[$product->brand_id]->name, true);
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['brand' => $name['ca'] ?? '']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
        });
    }
};
