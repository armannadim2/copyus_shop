<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_option_id')->constrained()->cascadeOnDelete();
            $table->string('value_key', 80);
            $table->json('label');                                   // translatable
            $table->decimal('price_modifier', 8, 4)->default(0);    // flat per-unit addition
            $table->enum('price_modifier_type', ['flat', 'percent'])->default('flat');
            $table->tinyInteger('production_days_modifier')->default(0);  // +/- days
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['print_option_id', 'value_key']);
            $table->index('print_option_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_option_values');
    }
};
