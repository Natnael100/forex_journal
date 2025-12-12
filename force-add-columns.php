<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Force adding profile columns...\n";

if (!Schema::hasColumn('users', 'username')) {
    echo "Adding username column...\n";
    Schema::table('users', function (Blueprint $table) {
        $table->string('username', 20)->nullable()->unique()->after('email');
    });
} else {
    echo "Username column already exists.\n";
}

Schema::table('users', function (Blueprint $table) {
    if (!Schema::hasColumn('users', 'profile_visibility')) {
        echo "Adding profile_visibility...\n";
        $table->enum('profile_visibility', ['public', 'analyst_only', 'private'])
              ->default('analyst_only')
              ->after('password');
        $table->index('profile_visibility');
    }

    if (!Schema::hasColumn('users', 'bio')) {
        echo "Adding bio...\n";
        $table->text('bio')->nullable()->after('profile_visibility');
    }

    if (!Schema::hasColumn('users', 'profile_photo')) {
        echo "Adding profile_photo...\n";
        $table->string('profile_photo')->nullable()->after('bio');
    }

    if (!Schema::hasColumn('users', 'cover_photo')) {
        echo "Adding cover_photo...\n";
        $table->string('cover_photo')->nullable()->after('profile_photo');
    }

    if (!Schema::hasColumn('users', 'country')) {
        echo "Adding country...\n";
        $table->string('country', 100)->nullable()->after('cover_photo');
    }

    if (!Schema::hasColumn('users', 'timezone')) {
        echo "Adding timezone...\n";
        $table->string('timezone', 100)->default('UTC')->after('country');
    }

    if (!Schema::hasColumn('users', 'experience_level')) {
        echo "Adding experience_level...\n";
        $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])
              ->nullable()->after('timezone');
        $table->index('experience_level');
    }

    if (!Schema::hasColumn('users', 'specialization')) {
        echo "Adding specialization...\n";
        $table->string('specialization', 100)->nullable()->after('experience_level');
    }

    if (!Schema::hasColumn('users', 'trading_style')) {
        echo "Adding trading_style...\n";
        $table->string('trading_style', 100)->nullable()->after('specialization');
    }

    if (!Schema::hasColumn('users', 'preferred_sessions')) {
        echo "Adding preferred_sessions...\n";
        $table->json('preferred_sessions')->nullable()->after('trading_style');
    }

    if (!Schema::hasColumn('users', 'favorite_pairs')) {
        echo "Adding favorite_pairs...\n";
        $table->json('favorite_pairs')->nullable()->after('preferred_sessions');
    }

    if (!Schema::hasColumn('users', 'profile_tags')) {
        echo "Adding profile_tags...\n";
        $table->json('profile_tags')->nullable()->after('favorite_pairs');
    }

    if (!Schema::hasColumn('users', 'social_links')) {
        echo "Adding social_links...\n";
        $table->json('social_links')->nullable()->after('profile_tags');
    }

    if (!Schema::hasColumn('users', 'show_last_active')) {
        echo "Adding show_last_active...\n";
        $table->boolean('show_last_active')->default(true)->after('social_links');
    }

    if (!Schema::hasColumn('users', 'profile_completed_at')) {
        echo "Adding profile_completed_at...\n";
        $table->timestamp('profile_completed_at')->nullable()->after('show_last_active');
    }

    if (!Schema::hasColumn('users', 'is_profile_verified')) {
        echo "Adding is_profile_verified...\n";
        $table->boolean('is_profile_verified')->default(false)->after('profile_completed_at');
    }
});

// Ensure migration is marked as run
$migrationName = '2025_12_11_140000_add_profile_fields_to_users_table';
$exists = DB::table('migrations')->where('migration', $migrationName)->exists();
if (!$exists) {
    echo "Marking migration as run in database...\n";
    DB::table('migrations')->insert([
        'migration' => $migrationName,
        'batch' => DB::table('migrations')->max('batch') + 1
    ]);
}

echo "Done!\n";
