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
        if (!Schema::hasTable('analyst_applications')) {
            Schema::create('analyst_applications', function (Blueprint $table) {
                $table->id();
                
                // Personal Information
                $table->string('name');
                $table->string('email')->unique();
                $table->string('country')->nullable();
                $table->string('timezone')->nullable();
                $table->string('phone')->nullable();
                
                // Professional Credentials
                $table->string('years_experience');
                $table->json('certifications')->nullable();
                $table->json('certificate_files')->nullable();
                $table->json('methodology')->nullable();
                $table->json('specializations')->nullable();
                
                // Coaching Experience
                $table->string('coaching_experience');
                $table->string('clients_coached');
                $table->string('coaching_style')->nullable();
                
                // Social Proof
                $table->string('track_record_url')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('twitter_handle')->nullable();
                $table->string('youtube_url')->nullable();
                $table->string('website_url')->nullable();
                
                // Application Statement
                $table->text('why_join');
                $table->text('unique_value');
                
                // Service Details
                $table->string('max_clients');
                $table->json('communication_methods')->nullable();
                
                // Admin Review
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('reviewed_at')->nullable();
                
                $table->timestamps();
                
                $table->index('status');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyst_applications');
    }
};
