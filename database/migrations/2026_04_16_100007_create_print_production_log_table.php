<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 100);
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('print_job_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_production_logs');
    }
};
