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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Saint Seiya
            $table->string('slug')->unique(); // Ex: saint-seiya
            $table->text('description')->nullable();
            $table->string('primary_color')->default('#6366f1'); // Couleur principale
            $table->string('secondary_color')->default('#8b5cf6'); // Couleur secondaire
            $table->string('accent_color')->default('#f59e0b'); // Couleur d'accent
            $table->string('background_image')->nullable(); // Image de fond optionnelle
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false); // Pour future monétisation
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
