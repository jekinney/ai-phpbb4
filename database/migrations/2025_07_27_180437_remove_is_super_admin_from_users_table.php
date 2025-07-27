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
            // Drop the old index first
            $table->dropIndex(['is_super_admin', 'is_banned']);
            // Drop the column
            $table->dropColumn('is_super_admin');
            // Add new index with just is_banned
            $table->index(['is_banned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the current index
            $table->dropIndex(['is_banned']);
            // Add the column back
            $table->boolean('is_super_admin')->default(false);
            // Recreate the old index
            $table->index(['is_super_admin', 'is_banned']);
        });
    }
};
