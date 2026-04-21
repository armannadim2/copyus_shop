// database/migrations/xxxx_create_products_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->json('name');
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->string('sku')->unique();
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->integer('min_order_quantity')->default(1);
            $table->string('unit')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('b2b_price', 10, 2)->nullable();  // Special B2B price
            $table->decimal('vat_rate', 5, 2)->default(21.00); // Belgium standard VAT
            $table->integer('stock')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('thumbnail')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
