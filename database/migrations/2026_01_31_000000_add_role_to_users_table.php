<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                // Determine previous column to place 'role' after, falling back to 'email' or 'id'
                $after = 'email';
                if (Schema::hasColumn('users', 'email_verified_at')) {
                    $after = 'email_verified_at';
                }
                
                $table->string('role')->default('trader')->after($after);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
