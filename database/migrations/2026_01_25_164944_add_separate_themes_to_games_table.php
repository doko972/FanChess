<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Ajouter les colonnes pour les thèmes séparés
            $table->foreignId('white_theme_id')->nullable()->after('theme_id')->constrained('themes')->nullOnDelete();
            $table->foreignId('black_theme_id')->nullable()->after('white_theme_id')->constrained('themes')->nullOnDelete();
        });

        // Migrer les données existantes : copier theme_id vers white_theme_id et black_theme_id
        DB::statement('UPDATE games SET white_theme_id = theme_id, black_theme_id = theme_id WHERE theme_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['white_theme_id']);
            $table->dropForeign(['black_theme_id']);
            $table->dropColumn(['white_theme_id', 'black_theme_id']);
        });
    }
};
