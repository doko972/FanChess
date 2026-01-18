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
            $table->boolean('is_admin')->default(false)->after('password');
            $table->foreignId('preferred_theme_id')->nullable()->after('is_admin');
            $table->integer('elo_rating')->default(1200)->after('preferred_theme_id');
            $table->integer('games_played')->default(0)->after('elo_rating');
            $table->integer('games_won')->default(0)->after('games_played');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'preferred_theme_id', 'elo_rating', 'games_played', 'games_won']);
        });
    }
};
