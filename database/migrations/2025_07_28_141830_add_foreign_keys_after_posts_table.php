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
        // Add foreign key constraint from forums to posts table
        Schema::table('forums', function (Blueprint $table) {
            $table->foreign('last_post_id')->references('id')->on('posts')->onDelete('set null');
        });
        
        // Add foreign key constraint from topics to posts table
        Schema::table('topics', function (Blueprint $table) {
            $table->foreign('last_post_id')->references('id')->on('posts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            $table->dropForeign(['last_post_id']);
        });
        
        Schema::table('topics', function (Blueprint $table) {
            $table->dropForeign(['last_post_id']);
        });
    }
};
