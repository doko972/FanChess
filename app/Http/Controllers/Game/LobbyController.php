<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Theme;
use Illuminate\Http\Request;

class LobbyController extends Controller
{
    /**
     * Affiche le lobby
     */
    public function index()
    {
        $user = auth()->user();

        // Parties en attente (créées par d'autres joueurs)
        $waitingGames = Game::with(['whitePlayer', 'theme'])
            ->waiting()
            ->pvp()
            ->where('white_player_id', '!=', $user->id)
            ->latest()
            ->get();

        // Mes parties en cours
        $myGames = Game::with(['whitePlayer', 'blackPlayer', 'theme'])
            ->inProgress()
            ->where(function ($query) use ($user) {
                $query->where('white_player_id', $user->id)
                    ->orWhere('black_player_id', $user->id);
            })
            ->latest()
            ->get();

        // Ma partie en attente (si existante)
        $myWaitingGame = Game::with('theme')
            ->waiting()
            ->where('white_player_id', $user->id)
            ->first();

        // Thèmes disponibles
        $themes = Theme::active()
            ->withCount('cards')
            ->having('cards_count', '>=', 12) // Seulement les thèmes complets
            ->orderBy('sort_order')
            ->get();

        // Statistiques du joueur
        $playerStats = [
            'elo' => $user->elo_rating,
            'games_played' => $user->games_played,
            'games_won' => $user->games_won,
            'win_rate' => $user->games_played > 0 
                ? round(($user->games_won / $user->games_played) * 100) 
                : 0,
        ];

        return view('game.lobby', compact(
            'waitingGames',
            'myGames',
            'myWaitingGame',
            'themes',
            'playerStats'
        ));
    }

    /**
     * Crée une nouvelle partie PvP
     */
    public function createGame(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:themes,id',
            'timer_enabled' => 'boolean',
            'timer_minutes' => 'required_if:timer_enabled,true|nullable|integer|in:1,3,5,10,15,30',
            'timer_increment' => 'nullable|integer|in:0,1,2,3,5,10',
        ]);

        $user = auth()->user();

        // Vérifier si l'utilisateur n'a pas déjà une partie en attente
        $existingWaiting = Game::waiting()
            ->where('white_player_id', $user->id)
            ->first();

        if ($existingWaiting) {
            return back()->with('error', 'Vous avez déjà une partie en attente. Annulez-la avant d\'en créer une nouvelle.');
        }

        $game = Game::create([
            'white_player_id' => $user->id,
            'theme_id' => $validated['theme_id'],
            'game_type' => 'pvp',
            'timer_enabled' => $request->boolean('timer_enabled'),
            'timer_minutes' => $validated['timer_minutes'] ?? null,
            'timer_increment' => $validated['timer_increment'] ?? 0,
            'status' => 'waiting',
        ]);

        return redirect()->route('game.waiting', $game->uuid);
    }

    /**
     * Crée une partie contre l'IA
     */
    public function createAiGame(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:themes,id',
            'ai_level' => 'required|integer|in:1,5,10,15,20',
            'player_color' => 'required|in:white,black,random',
            'timer_enabled' => 'boolean',
            'timer_minutes' => 'required_if:timer_enabled,true|nullable|integer|in:1,3,5,10,15,30',
        ]);

        $user = auth()->user();

        // Déterminer la couleur du joueur
        $playerColor = $validated['player_color'];
        if ($playerColor === 'random') {
            $playerColor = rand(0, 1) ? 'white' : 'black';
        }

        $gameData = [
            'theme_id' => $validated['theme_id'],
            'game_type' => 'ai',
            'ai_level' => $validated['ai_level'],
            'timer_enabled' => $request->boolean('timer_enabled'),
            'timer_minutes' => $validated['timer_minutes'] ?? null,
            'status' => 'in_progress',
            'started_at' => now(),
        ];

        if ($playerColor === 'white') {
            $gameData['white_player_id'] = $user->id;
        } else {
            $gameData['black_player_id'] = $user->id;
            // L'IA joue en premier si elle est blanche
        }

        $game = Game::create($gameData);

        return redirect()->route('game.play', $game->uuid);
    }

    /**
     * Page d'attente pour une partie
     */
    public function waiting(string $uuid)
    {
        $game = Game::with(['whitePlayer', 'theme'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (!$game->isWaiting()) {
            return redirect()->route('game.play', $game->uuid);
        }

        // Seul le créateur peut voir la page d'attente
        if ($game->white_player_id !== auth()->id()) {
            return redirect()->route('lobby');
        }

        return view('game.waiting', compact('game'));
    }

    /**
     * Rejoindre une partie
     */
    public function joinGame(string $uuid)
    {
        $game = Game::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if (!$game->isWaiting()) {
            return back()->with('error', 'Cette partie n\'est plus disponible.');
        }

        if ($game->white_player_id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas rejoindre votre propre partie.');
        }

        $game->update([
            'black_player_id' => $user->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // TODO: Broadcaster l'événement pour notifier le joueur blanc

        return redirect()->route('game.play', $game->uuid);
    }

    /**
     * Annuler une partie en attente
     */
    public function cancelGame(string $uuid)
    {
        $game = Game::where('uuid', $uuid)->firstOrFail();

        if ($game->white_player_id !== auth()->id()) {
            abort(403);
        }

        if (!$game->isWaiting()) {
            return back()->with('error', 'Impossible d\'annuler cette partie.');
        }

        $game->delete();

        return redirect()->route('lobby')->with('success', 'Partie annulée.');
    }
}
