<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameMove;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Affiche la partie
     */
    public function play(string $uuid)
    {
        $game = Game::with([
            'whitePlayer',
            'blackPlayer',
            'theme.cards',
            'moves'
        ])->where('uuid', $uuid)->firstOrFail();

        $user = auth()->user();
        $playerColor = $game->getPlayerColor($user);

        // Vérifier que le joueur fait partie de la partie
        if (!$playerColor && !$game->isAiGame()) {
            abort(403, 'Vous ne participez pas à cette partie.');
        }

        // Organiser les cartes par type et couleur
        $cards = [];
        if ($game->theme) {
            foreach ($game->theme->cards as $card) {
                $cards[$card->color][$card->piece_type] = $card;
            }
        }

        return view('game.play', compact('game', 'playerColor', 'cards'));
    }

    /**
     * Effectue un coup (API)
     */
    public function makeMove(Request $request, string $uuid)
    {
        $game = Game::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Vérifications
        if (!$game->isInProgress()) {
            return response()->json(['error' => 'La partie n\'est pas en cours.'], 400);
        }

        if (!$game->isPlayerTurn($user) && !$game->isAiGame()) {
            return response()->json(['error' => 'Ce n\'est pas votre tour.'], 400);
        }

        $validated = $request->validate([
            'from' => 'required|string|size:2',
            'to' => 'required|string|size:2',
            'promotion' => 'nullable|string|in:q,r,b,n',
            'san' => 'required|string',
            'fen' => 'required|string',
            'piece' => 'required|string|size:1',
            'captured' => 'nullable|string|size:1',
            'is_check' => 'boolean',
            'is_checkmate' => 'boolean',
            'is_castling' => 'boolean',
            'is_en_passant' => 'boolean',
        ]);

        // Enregistrer le coup
        $move = GameMove::create([
            'game_id' => $game->id,
            'player_id' => $user->id,
            'move_number' => $game->move_count + 1,
            'move_san' => $validated['san'],
            'move_uci' => $validated['from'] . $validated['to'] . ($validated['promotion'] ?? ''),
            'from_square' => $validated['from'],
            'to_square' => $validated['to'],
            'piece' => $validated['piece'],
            'captured_piece' => $validated['captured'] ?? null,
            'promotion' => $validated['promotion'] ?? null,
            'is_check' => $request->boolean('is_check'),
            'is_checkmate' => $request->boolean('is_checkmate'),
            'is_castling' => $request->boolean('is_castling'),
            'is_en_passant' => $request->boolean('is_en_passant'),
            'fen_after' => $validated['fen'],
        ]);

        // Mettre à jour la partie
        $updateData = [
            'current_fen' => $validated['fen'],
            'move_count' => $game->move_count + 1,
            'current_turn' => $game->current_turn === 'white' ? 'black' : 'white',
        ];

        // Vérifier fin de partie
        if ($request->boolean('is_checkmate')) {
            $updateData['status'] = 'completed';
            $updateData['winner_id'] = $user->id;
            $updateData['end_reason'] = 'checkmate';
            $updateData['ended_at'] = now();
        }

        $game->update($updateData);

        // TODO: Broadcaster le coup via WebSocket

        return response()->json([
            'success' => true,
            'move' => $move,
            'game_status' => $game->fresh()->status,
        ]);
    }

    /**
     * Abandonner la partie
     */
    public function resign(string $uuid)
    {
        $game = Game::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        $playerColor = $game->getPlayerColor($user);
        if (!$playerColor) {
            abort(403);
        }

        if (!$game->isInProgress()) {
            return back()->with('error', 'Impossible d\'abandonner cette partie.');
        }

        // Le gagnant est l'autre joueur
        $winnerId = $playerColor === 'white' 
            ? $game->black_player_id 
            : $game->white_player_id;

        $game->update([
            'status' => 'completed',
            'winner_id' => $winnerId,
            'end_reason' => 'resignation',
            'ended_at' => now(),
        ]);

        return redirect()->route('lobby')->with('info', 'Vous avez abandonné la partie.');
    }

    /**
     * Proposer match nul
     */
    public function offerDraw(string $uuid)
    {
        // TODO: Implémenter via WebSocket
        return response()->json(['success' => true, 'message' => 'Proposition envoyée.']);
    }

    /**
     * Récupère l'état actuel de la partie (API)
     */
    public function getState(string $uuid)
    {
        $game = Game::with(['moves' => function ($query) {
            $query->latest()->take(1);
        }])->where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'status' => $game->status,
            'current_fen' => $game->current_fen,
            'current_turn' => $game->current_turn,
            'move_count' => $game->move_count,
            'last_move' => $game->moves->first(),
            'white_time' => $game->white_time_remaining,
            'black_time' => $game->black_time_remaining,
        ]);
    }

    /**
     * Historique des parties du joueur
     */
    public function history()
    {
        $user = auth()->user();

        $games = Game::with(['whitePlayer', 'blackPlayer', 'winner', 'theme'])
            ->where(function ($query) use ($user) {
                $query->where('white_player_id', $user->id)
                    ->orWhere('black_player_id', $user->id);
            })
            ->whereIn('status', ['completed', 'draw', 'abandoned'])
            ->latest('ended_at')
            ->paginate(20);

        return view('game.history', compact('games'));
    }

    /**
     * Replay d'une partie
     */
    public function replay(string $uuid)
    {
        $game = Game::with([
            'whitePlayer',
            'blackPlayer',
            'theme.cards',
            'moves'
        ])->where('uuid', $uuid)->firstOrFail();

        if (!$game->isFinished()) {
            return redirect()->route('game.play', $game->uuid);
        }

        $cards = [];
        if ($game->theme) {
            foreach ($game->theme->cards as $card) {
                $cards[$card->color][$card->piece_type] = $card;
            }
        }

        return view('game.replay', compact('game', 'cards'));
    }
}
