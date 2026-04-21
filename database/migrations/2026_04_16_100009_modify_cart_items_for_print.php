<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // MySQL uses the composite unique as the backing index for the user_id FK.
            // Add a standalone index first so the FK has a home before we drop the composite.
            $table->index('user_id', 'cart_items_user_id_fk_index');

            // Drop the unique index that would prevent multiple print jobs per user
            $table->dropUnique(['user_id', 'product_id', 'type']);

            // Make product_id nullable (print items have no product)
            $table->foreignId('product_id')->nullable()->change();

            // Print job reference (null for regular product cart items)
            $table->foreignId('print_job_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->nullOnDelete();

            // Frozen unit price (used by print items; product items resolve live)
            $table->decimal('unit_price', 10, 4)
                ->nullable()
                ->after('quantity');

            // Denormalised config snapshot for cart display
            $table->json('configuration_snapshot')
                ->nullable()
                ->after('unit_price');

            $table->index('print_job_id');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['print_job_id']);
            $table->dropIndex(['print_job_id']);
            $table->dropColumn(['print_job_id', 'unit_price', 'configuration_snapshot']);
            $table->foreignId('product_id')->nullable(false)->change();
            $table->unique(['user_id', 'product_id', 'type']);
            $table->dropIndex('cart_items_user_id_fk_index');
        });
    }
};
