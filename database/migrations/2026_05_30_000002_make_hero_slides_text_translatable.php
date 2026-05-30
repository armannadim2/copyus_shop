<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['eyebrow', 'title']);
        });

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->json('eyebrow')->nullable()->after('image');
            $table->json('title')->nullable()->after('eyebrow');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['eyebrow', 'title']);
        });

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('eyebrow')->nullable()->after('image');
            $table->string('title')->nullable()->after('eyebrow');
        });
    }
};
