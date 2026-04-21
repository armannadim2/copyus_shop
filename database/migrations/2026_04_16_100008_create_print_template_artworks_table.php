<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_template_artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_template_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('mime_type', 80)->nullable();
            $table->json('label')->nullable();                       // translatable caption
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('print_template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_template_artworks');
    }
};
