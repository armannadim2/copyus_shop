<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('stock_status', ['in_stock', 'pre_order'])
                  ->default('in_stock')
                  ->after('vat_rate');

            $table->dropColumn(['stock', 'low_stock_threshold', 'notify_low_stock']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_status');

            $table->integer('stock')->default(0)->after('vat_rate');
            $table->unsignedInteger('low_stock_threshold')->nullable();
            $table->boolean('notify_low_stock')->default(false);
        });
    }
};
