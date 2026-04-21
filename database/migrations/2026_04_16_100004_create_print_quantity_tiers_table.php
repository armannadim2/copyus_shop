<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_quantity_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('min_quantity');
            $table->decimal('discount_percent', 5, 2);               // e.g. 10.00 = 10% off
            $table->json('label')->nullable();                        // translatable "100+ units"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['print_template_id', 'min_quantity']);
            $table->index('print_template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_quantity_tiers');
    }
};
