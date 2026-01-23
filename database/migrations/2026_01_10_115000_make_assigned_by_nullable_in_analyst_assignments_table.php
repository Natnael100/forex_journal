<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks for SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }
        
        Schema::table('analyst_assignments', function (Blueprint $table) {
            $table->foreignId('assigned_by')->nullable()->change();
        });
        
        // Re-enable foreign key checks for SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    public function down(): void
    {
        Schema::table('analyst_assignments', function (Blueprint $table) {
            $table->foreignId('assigned_by')->nullable(false)->change();
        });
    }
};
