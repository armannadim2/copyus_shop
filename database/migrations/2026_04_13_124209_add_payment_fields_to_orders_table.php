<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_reference');
            $table->text('admin_notes')->nullable()->after('notes');
            $table->string('tracking_number')->nullable()->after('admin_notes');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->timestamp('cancelled_at')->nullable()->after('tracking_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference', 'payment_confirmed_at',
                'admin_notes', 'tracking_number', 'tracking_url', 'cancelled_at',
            ]);
        });
    }
};
