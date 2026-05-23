<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add a plain string column alongside the old JSON one
        Schema::table('brands', function (Blueprint $table) {
            $table->string('name_text', 255)->default('')->after('id');
        });

        // Copy best available translation into the new column
        DB::statement("
            UPDATE brands
            SET name_text = COALESCE(
                NULLIF(JSON_UNQUOTE(JSON_EXTRACT(name, '$.ca')), 'null'),
                NULLIF(JSON_UNQUOTE(JSON_EXTRACT(name, '$.es')), 'null'),
                NULLIF(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')), 'null'),
                ''
            )
            WHERE name IS NOT NULL
        ");

        // Drop the JSON column then rename the new one
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->renameColumn('name_text', 'name');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->renameColumn('name', 'name_text');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->json('name')->after('id');
        });

        DB::statement("
            UPDATE brands
            SET name = JSON_OBJECT('ca', name_text, 'es', name_text, 'en', name_text)
        ");

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('name_text');
        });
    }
};
