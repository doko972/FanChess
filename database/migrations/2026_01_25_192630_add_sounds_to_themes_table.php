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
        Schema::table('themes', function (Blueprint $table) {
            // Musique de fond
            $table->string('music_file')->nullable()->after('accent_color');

            // Sons d'actions
            $table->string('sound_move')->nullable()->after('music_file');
            $table->string('sound_capture')->nullable()->after('sound_move');
            $table->string('sound_check')->nullable()->after('sound_capture');
            $table->string('sound_checkmate')->nullable()->after('sound_check');
            $table->string('sound_victory')->nullable()->after('sound_checkmate');
            $table->string('sound_defeat')->nullable()->after('sound_victory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn([
                'music_file',
                'sound_move',
                'sound_capture',
                'sound_check',
                'sound_checkmate',
                'sound_victory',
                'sound_defeat',
            ]);
        });
    }
};
