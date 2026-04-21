<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');                                    // translatable ca/es/en
            $table->json('description')->nullable();
            $table->string('icon')->nullable();                      // emoji or icon name
            $table->decimal('base_price', 10, 4);
            $table->decimal('vat_rate', 5, 2)->default(21.00);
            $table->unsignedTinyInteger('base_production_days')->default(3);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_templates');
    }
};
