<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Game;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Affiche le dashboard admin
     */
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'themes_count' => Theme::count(),
            'themes_active' => Theme::active()->count(),
            'cards_count' => Card::count(),
            'games_total' => Game::count(),
            'games_in_progress' => Game::inProgress()->count(),
            'games_today' => Game::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentGames = Game::with(['whitePlayer', 'blackPlayer', 'theme'])
            ->latest()
            ->take(5)
            ->get();

        $incompleteThemes = Theme::active()
            ->withCount('cards')
            ->having('cards_count', '<', 12)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentGames', 'incompleteThemes'));
    }
}
