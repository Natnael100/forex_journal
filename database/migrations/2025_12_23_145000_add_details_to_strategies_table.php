<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('strategies', function (Blueprint $table) {
            $table->string('status')->default('active')->after('description'); // active, testing, archived
            $table->json('tags')->nullable()->after('status');
            $table->json('rules')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('strategies', function (Blueprint $table) {
            $table->dropColumn(['status', 'tags', 'rules']);
        });
    }
};
