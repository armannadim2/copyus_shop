<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('print_template_id')->constrained()->restrictOnDelete();
            $table->enum('status', [
                'draft', 'in_cart', 'ordered', 'in_production', 'completed', 'cancelled'
            ])->default('draft');
            $table->json('configuration');                           // {option_key: value_key, ...}
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 4)->nullable();        // frozen at cart-add time
            $table->decimal('total_price', 10, 4)->nullable();       // unit_price * quantity
            $table->unsignedInteger('production_days')->nullable();  // frozen at cart-add time
            $table->string('artwork_path')->nullable();
            $table->text('artwork_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->date('expected_delivery_at')->nullable();
            $table->timestamp('produced_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('print_template_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
