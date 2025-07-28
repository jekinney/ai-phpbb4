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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('is_sticky')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->unsignedBigInteger('last_post_id')->nullable();
            $table->foreignId('last_post_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_post_at')->nullable();
            $table->timestamps();
            
            $table->index(['forum_id', 'is_sticky', 'last_post_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
