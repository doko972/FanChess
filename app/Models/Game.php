<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'white_player_id',
        'black_player_id',
        'theme_id',
        'white_theme_id',
        'black_theme_id',
        'winner_id',
        'status',
        'game_type',
        'ai_level',
        'timer_enabled',
        'timer_minutes',
        'timer_increment',
        'white_time_remaining',
        'black_time_remaining',
        'pgn',
        'current_fen',
        'current_turn',
        'move_count',
        'end_reason',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'timer_enabled' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Statuts possibles
     */
    public const STATUSES = [
        'waiting' => 'En attente',
        'in_progress' => 'En cours',
        'completed' => 'Terminée',
        'abandoned' => 'Abandonnée',
        'draw' => 'Match nul',
    ];

    /**
     * Niveaux d'IA
     */
    public const AI_LEVELS = [
        1 => 'Débutant',
        5 => 'Facile',
        10 => 'Intermédiaire',
        15 => 'Difficile',
        20 => 'Expert',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            if (empty($game->uuid)) {
                $game->uuid = Str::uuid();
            }
            
            // Initialiser le timer si activé
            if ($game->timer_enabled && $game->timer_minutes) {
                $seconds = $game->timer_minutes * 60;
                $game->white_time_remaining = $seconds;
                $game->black_time_remaining = $seconds;
            }
        });
    }

    /**
     * Joueur blanc
     */
    public function whitePlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'white_player_id');
    }

    /**
     * Joueur noir
     */
    public function blackPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'black_player_id');
    }

    /**
     * Gagnant
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Thème utilisé (legacy - pour compatibilité)
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Thème des pièces blanches
     */
    public function whiteTheme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'white_theme_id');
    }

    /**
     * Thème des pièces noires
     */
    public function blackTheme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'black_theme_id');
    }

    /**
     * Coups de la partie
     */
    public function moves(): HasMany
    {
        return $this->hasMany(GameMove::class)->orderBy('move_number');
    }

    /**
     * Vérifie si c'est une partie contre l'IA
     */
    public function isAiGame(): bool
    {
        return $this->game_type === 'ai';
    }

    /**
     * Vérifie si la partie est en attente
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Vérifie si la partie est en cours
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifie si la partie est terminée
     */
    public function isFinished(): bool
    {
        return in_array($this->status, ['completed', 'abandoned', 'draw']);
    }

    /**
     * Vérifie si c'est le tour du joueur donné
     */
    public function isPlayerTurn(User $user): bool
    {
        if ($this->current_turn === 'white') {
            return $this->white_player_id === $user->id;
        }
        return $this->black_player_id === $user->id;
    }

    /**
     * Récupère la couleur du joueur
     */
    public function getPlayerColor(User $user): ?string
    {
        if ($this->white_player_id === $user->id) {
            return 'white';
        }
        if ($this->black_player_id === $user->id) {
            return 'black';
        }
        return null;
    }

    /**
     * Nom du statut en français
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Scope pour les parties en attente
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope pour les parties en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les parties PvP
     */
    public function scopePvp($query)
    {
        return $query->where('game_type', 'pvp');
    }
}
