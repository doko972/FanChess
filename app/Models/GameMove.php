<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMove extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'move_number',
        'move_san',
        'move_uci',
        'from_square',
        'to_square',
        'piece',
        'captured_piece',
        'promotion',
        'is_check',
        'is_checkmate',
        'is_castling',
        'is_en_passant',
        'fen_after',
        'time_spent',
    ];

    protected $casts = [
        'is_check' => 'boolean',
        'is_checkmate' => 'boolean',
        'is_castling' => 'boolean',
        'is_en_passant' => 'boolean',
    ];

    /**
     * Noms des pièces
     */
    public const PIECE_NAMES = [
        'p' => 'Pion',
        'n' => 'Cavalier',
        'b' => 'Fou',
        'r' => 'Tour',
        'q' => 'Dame',
        'k' => 'Roi',
    ];

    /**
     * La partie
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Le joueur qui a fait le coup
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    /**
     * Nom de la pièce en français
     */
    public function getPieceNameAttribute(): string
    {
        return self::PIECE_NAMES[strtolower($this->piece)] ?? $this->piece;
    }

    /**
     * Description du coup
     */
    public function getDescriptionAttribute(): string
    {
        $desc = $this->piece_name;
        
        if ($this->is_castling) {
            return $this->move_san === 'O-O' ? 'Petit roque' : 'Grand roque';
        }
        
        $desc .= " {$this->from_square} → {$this->to_square}";
        
        if ($this->captured_piece) {
            $capturedName = self::PIECE_NAMES[strtolower($this->captured_piece)] ?? $this->captured_piece;
            $desc .= " (capture {$capturedName})";
        }
        
        if ($this->promotion) {
            $promoName = self::PIECE_NAMES[strtolower($this->promotion)] ?? $this->promotion;
            $desc .= " → Promotion en {$promoName}";
        }
        
        if ($this->is_checkmate) {
            $desc .= ' - ÉCHEC ET MAT!';
        } elseif ($this->is_check) {
            $desc .= ' - Échec!';
        }
        
        return $desc;
    }
}
