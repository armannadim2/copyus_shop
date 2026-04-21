<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_template_id')->constrained()->cascadeOnDelete();
            $table->string('key', 80);
            $table->json('label');                                   // translatable
            $table->enum('input_type', ['select', 'radio', 'toggle', 'number'])->default('select');
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['print_template_id', 'key']);
            $table->index('print_template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_options');
    }
};
