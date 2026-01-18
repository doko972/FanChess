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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained()->onDelete('cascade');
            $table->enum('piece_type', ['king', 'queen', 'rook', 'bishop', 'knight', 'pawn']);
            $table->enum('color', ['white', 'black']); // Couleur de la pièce (camp)
            $table->string('name'); // Ex: Athéna, Seiya, Shiryu
            $table->text('description')->nullable(); // Description du personnage
            $table->string('quote')->nullable(); // Citation culte du personnage
            $table->string('image')->nullable(); // Image principale
            $table->string('image_evolution')->nullable(); // Image évolution (future)
            $table->integer('attack_visual')->default(0); // Stats décoratives (0-100)
            $table->integer('defense_visual')->default(0); // Stats décoratives (0-100)
            $table->integer('speed_visual')->default(0); // Stats décoratives (0-100)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Une seule carte par type de pièce, couleur et thème
            $table->unique(['theme_id', 'piece_type', 'color']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
