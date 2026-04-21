<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_terms')->nullable()->after('payment_status')
                ->comment('Snapshot of company payment terms at order time (e.g. net_30)');
            $table->date('payment_due_at')->nullable()->after('payment_terms')
                ->comment('Invoice due date derived from payment terms');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_terms', 'payment_due_at']);
        });
    }
};
