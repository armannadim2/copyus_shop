<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_print_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('print_template_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->json('configuration');
            $table->unsignedInteger('quantity')->default(100);
            $table->text('artwork_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'print_template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_print_configs');
    }
};
