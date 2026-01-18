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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // Identifiant unique pour les URLs
            $table->foreignId('white_player_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('black_player_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('theme_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->enum('status', [
                'waiting',      // En attente d'un adversaire
                'in_progress',  // Partie en cours
                'completed',    // Partie terminée
                'abandoned',    // Partie abandonnée
                'draw'          // Match nul
            ])->default('waiting');
            
            $table->enum('game_type', ['pvp', 'ai'])->default('pvp'); // Joueur vs Joueur ou vs IA
            $table->integer('ai_level')->nullable(); // Niveau de l'IA (1-20)
            
            // Timer
            $table->boolean('timer_enabled')->default(false);
            $table->integer('timer_minutes')->nullable(); // Minutes par joueur
            $table->integer('timer_increment')->default(0); // Incrément en secondes
            $table->integer('white_time_remaining')->nullable(); // Temps restant en secondes
            $table->integer('black_time_remaining')->nullable();
            
            // État de la partie
            $table->text('pgn')->nullable(); // Notation PGN complète
            $table->string('current_fen')->default('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1'); // Position actuelle
            $table->enum('current_turn', ['white', 'black'])->default('white');
            $table->integer('move_count')->default(0);
            
            // Résultat
            $table->enum('end_reason', [
                'checkmate',
                'resignation',
                'timeout',
                'stalemate',
                'draw_agreement',
                'insufficient_material',
                'threefold_repetition',
                'fifty_moves'
            ])->nullable();
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
