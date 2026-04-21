<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_compatibility_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_template_id')->constrained()->cascadeOnDelete();
            $table->enum('rule_type', ['incompatible', 'requires', 'warning'])->default('incompatible');
            $table->string('condition_option_key', 80);  // option key that triggers the rule
            $table->string('condition_value_key', 80);   // value key that triggers it
            $table->string('target_option_key', 80);     // affected option
            $table->string('target_value_key', 80)->nullable(); // null = entire option is affected
            $table->json('message');                     // translatable user-facing text
            $table->timestamps();

            $table->index('print_template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_compatibility_rules');
    }
};
