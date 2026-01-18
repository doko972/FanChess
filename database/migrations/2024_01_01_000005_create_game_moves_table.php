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
        Schema::create('game_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('move_number');
            $table->string('move_san'); // Notation SAN (ex: e4, Nf3, O-O)
            $table->string('move_uci'); // Notation UCI (ex: e2e4, g1f3)
            $table->string('from_square', 2); // Case de départ (ex: e2)
            $table->string('to_square', 2); // Case d'arrivée (ex: e4)
            $table->string('piece', 1); // Pièce déplacée (p, n, b, r, q, k)
            $table->string('captured_piece', 1)->nullable(); // Pièce capturée
            $table->string('promotion', 1)->nullable(); // Promotion (q, r, b, n)
            $table->boolean('is_check')->default(false);
            $table->boolean('is_checkmate')->default(false);
            $table->boolean('is_castling')->default(false);
            $table->boolean('is_en_passant')->default(false);
            $table->text('fen_after'); // Position FEN après le coup
            $table->integer('time_spent')->nullable(); // Temps passé en secondes
            $table->timestamps();

            $table->index(['game_id', 'move_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_moves');
    }
};
