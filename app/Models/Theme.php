<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_image',
        'music_file',
        'sound_move',
        'sound_capture',
        'sound_check',
        'sound_checkmate',
        'sound_victory',
        'sound_defeat',
        'is_active',
        'is_premium',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($theme) {
            if (empty($theme->slug)) {
                $theme->slug = Str::slug($theme->name);
            }
        });
    }

    /**
     * Les cartes du thème
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Les parties utilisant ce thème
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Les utilisateurs préférant ce thème
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'preferred_theme_id');
    }

    /**
     * Vérifie si le thème a toutes ses cartes
     */
    public function isComplete(): bool
    {
        // 6 types de pièces x 2 couleurs = 12 cartes
        return $this->cards()->count() >= 12;
    }

    /**
     * Récupère une carte par type et couleur
     */
    public function getCard(string $pieceType, string $color): ?Card
    {
        return $this->cards()
            ->where('piece_type', $pieceType)
            ->where('color', $color)
            ->first();
    }

    /**
     * Scope pour les thèmes actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les thèmes gratuits
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }
}
