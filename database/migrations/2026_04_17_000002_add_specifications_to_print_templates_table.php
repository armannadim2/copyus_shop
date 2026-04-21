<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_templates', function (Blueprint $table) {
            $table->string('specifications_path')->nullable()->after('is_active');
            $table->string('specifications_label')->nullable()->after('specifications_path');
        });
    }

    public function down(): void
    {
        Schema::table('print_templates', function (Blueprint $table) {
            $table->dropColumn(['specifications_path', 'specifications_label']);
        });
    }
};
