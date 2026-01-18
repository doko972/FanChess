<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'piece_type',
        'color',
        'name',
        'description',
        'quote',
        'image',
        'image_evolution',
        'attack_visual',
        'defense_visual',
        'speed_visual',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'attack_visual' => 'integer',
        'defense_visual' => 'integer',
        'speed_visual' => 'integer',
    ];

    /**
     * Types de pièces disponibles
     */
    public const PIECE_TYPES = [
        'king' => 'Roi',
        'queen' => 'Dame',
        'rook' => 'Tour',
        'bishop' => 'Fou',
        'knight' => 'Cavalier',
        'pawn' => 'Pion',
    ];

    /**
     * Couleurs disponibles
     */
    public const COLORS = [
        'white' => 'Blanc',
        'black' => 'Noir',
    ];

    /**
     * Le thème de la carte
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * URL de l'image principale
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    /**
     * URL de l'image évolution
     */
    public function getImageEvolutionUrlAttribute(): ?string
    {
        if ($this->image_evolution) {
            return Storage::url($this->image_evolution);
        }
        return null;
    }

    /**
     * Nom du type de pièce en français
     */
    public function getPieceTypeNameAttribute(): string
    {
        return self::PIECE_TYPES[$this->piece_type] ?? $this->piece_type;
    }

    /**
     * Nom de la couleur en français
     */
    public function getColorNameAttribute(): string
    {
        return self::COLORS[$this->color] ?? $this->color;
    }

    /**
     * Nom complet de la carte
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->piece_type_name} {$this->color_name})";
    }

    /**
     * Scope pour les cartes actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope par type de pièce
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('piece_type', $type);
    }

    /**
     * Scope par couleur
     */
    public function scopeOfColor($query, string $color)
    {
        return $query->where('color', $color);
    }
}
