<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cif_vat')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 5)->default('ES');
            $table->enum('payment_terms', ['immediate', 'net_15', 'net_30', 'net_60', 'net_90'])->default('immediate');
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->decimal('credit_used', 10, 2)->default(0);
            $table->decimal('approval_threshold', 10, 2)->nullable()->comment('Orders above this amount require manager approval');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('company_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->enum('role', ['manager', 'buyer', 'viewer'])->default('buyer');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['token']);
            $table->index(['company_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_invitations');
        Schema::dropIfExists('companies');
    }
};
