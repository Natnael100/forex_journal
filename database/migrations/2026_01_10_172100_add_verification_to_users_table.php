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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'analyst_verification_status')) {
                $table->enum('analyst_verification_status', ['pending', 'verified', 'rejected', 'suspended'])
                      ->default('pending')
                      ->after('remember_token');
            }
            
            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('analyst_verification_status');
            }
            
            if (!Schema::hasColumn('users', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')->after('verified_at');
            }
            
            if (!Schema::hasColumn('users', 'application_id')) {
                $table->foreignId('application_id')->nullable()->constrained('analyst_applications')->onDelete('set null')->after('verified_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['application_id']);
            $table->dropColumn([
                'analyst_verification_status',
                'verified_at',
                'verified_by',
                'application_id'
            ]);
        });
    }
};
