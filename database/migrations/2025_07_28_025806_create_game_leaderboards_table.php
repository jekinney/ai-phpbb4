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
        Schema::create('game_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rank');
            $table->decimal('best_score', 15, 2);
            $table->integer('best_time')->nullable(); // in seconds
            $table->integer('total_games_played')->default(1);
            $table->timestamp('last_played_at');
            $table->timestamps();
            
            $table->unique(['game_id', 'user_id']);
            $table->index(['game_id', 'rank']);
            $table->index(['game_id', 'best_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_leaderboards');
    }
};
