<?php
// database/migrations/xxxx_create_quotations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();          // CQT-2024-00001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('converted_to_order_id')
                ->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('status', [
                'pending',
                'reviewing',
                'quoted',
                'accepted',
                'rejected',
                'expired',
                'converted'
            ])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_subtotal', 10, 2)->nullable();
            $table->decimal('quoted_vat', 10, 2)->nullable();
            $table->decimal('quoted_total', 10, 2)->nullable();
            $table->date('valid_until')->nullable();
            $table->string('locale')->default('ca');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->json('product_snapshot')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 4)->nullable();
            $table->decimal('quoted_price', 10, 2)->nullable(); // Admin fills this
            $table->decimal('vat_rate', 5, 2)->nullable();
            $table->decimal('vat_amount', 10, 4)->nullable();
            $table->decimal('total', 10, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
