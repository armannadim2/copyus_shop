<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
            $table->enum('company_role', ['owner', 'manager', 'buyer', 'viewer'])
                ->nullable()
                ->after('company_id');
            $table->decimal('spending_limit', 10, 2)
                ->nullable()
                ->after('company_role')
                ->comment('Per-user monthly spending cap, null = unlimited');
            $table->timestamp('cart_recovery_sent_at')
                ->nullable()
                ->after('spending_limit');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['company_role', 'spending_limit', 'cart_recovery_sent_at']);
        });
    }
};
