<?php

use App\Models\Game;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Canal privé pour une partie de jeu.
 * Seuls les joueurs de la partie peuvent s'y connecter.
 */
Broadcast::channel('game.{uuid}', function ($user, $uuid) {
    $game = Game::where('uuid', $uuid)->first();

    if (!$game) {
        return false;
    }

    // Le créateur de la partie (joueur blanc) peut toujours rejoindre
    // même si black_player_id n'est pas encore défini (en attente)
    return $game->white_player_id === $user->id
        || $game->black_player_id === $user->id;
});
