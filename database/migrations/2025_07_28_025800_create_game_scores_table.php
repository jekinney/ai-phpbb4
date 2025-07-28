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
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 15, 2);
            $table->integer('time_taken')->nullable(); // in seconds
            $table->json('metadata')->nullable(); // Additional game data
            $table->timestamp('achieved_at');
            $table->timestamps();
            
            $table->index(['game_id', 'score']);
            $table->index(['game_id', 'user_id', 'achieved_at']);
            $table->index(['user_id', 'achieved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_scores');
    }
};
