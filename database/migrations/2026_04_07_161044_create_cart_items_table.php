<?php
// database/migrations/xxxx_create_cart_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->enum('type', ['cart', 'quote'])->default('cart'); // Cart or Quote basket
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
