<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Update existing data first
        DB::statement("UPDATE users SET role = 'approved' WHERE role = 'b2b_approved'");
        DB::statement("UPDATE users SET role = 'pending'  WHERE role = 'b2b_pending'");

        // Alter the enum (MySQL-only; SQLite keeps role as TEXT so no schema change needed).
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','approved','pending','rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','approved','pending','rejected','b2b_approved','b2b_pending') NOT NULL DEFAULT 'pending'");
        }
        DB::statement("UPDATE users SET role = 'b2b_approved' WHERE role = 'approved'");
        DB::statement("UPDATE users SET role = 'b2b_pending'  WHERE role = 'pending'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','b2b_approved','b2b_pending','rejected') NOT NULL DEFAULT 'b2b_pending'");
        }
    }
};
