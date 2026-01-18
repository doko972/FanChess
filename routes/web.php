<?php

use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\Game\LobbyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('lobby');
    }
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Routes d'Authentification (personnalisées pour sécurité)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Inscription - Route personnalisée
    Route::get('/rejoindre', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/rejoindre', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])
        ->middleware(['throttle:5,1', 'honeypot']);

    // Connexion - Route personnalisée
    Route::get('/connexion', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/connexion', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1');

    // Mot de passe oublié
    Route::get('/mot-de-passe-oublie', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/mot-de-passe-oublie', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email')
        ->middleware('throttle:3,1');

    // Réinitialisation mot de passe
    Route::get('/reinitialiser-mot-de-passe/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Déconnexion
    Route::post('/deconnexion', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Vérification email
    Route::get('/verification-email', [App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('/verification-email/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/verification-email/renvoyer', [App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Confirmation mot de passe
    Route::get('/confirmer-mot-de-passe', [App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/confirmer-mot-de-passe', [App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    // Mise à jour mot de passe
    Route::put('/mot-de-passe', [App\Http\Controllers\Auth\PasswordController::class, 'update'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Routes Joueur (authentifié + email vérifié)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Profil
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lobby
    Route::get('/lobby', [LobbyController::class, 'index'])->name('lobby');
    Route::post('/lobby/creer-partie', [LobbyController::class, 'createGame'])->name('lobby.create');
    Route::post('/lobby/creer-partie-ia', [LobbyController::class, 'createAiGame'])->name('lobby.create-ai');
    Route::get('/lobby/attente/{uuid}', [LobbyController::class, 'waiting'])->name('game.waiting');
    Route::post('/lobby/rejoindre/{uuid}', [LobbyController::class, 'joinGame'])->name('lobby.join');
    Route::delete('/lobby/annuler/{uuid}', [LobbyController::class, 'cancelGame'])->name('lobby.cancel');

    // Jeu
    Route::get('/partie/{uuid}', [GameController::class, 'play'])->name('game.play');
    Route::post('/partie/{uuid}/coup', [GameController::class, 'makeMove'])->name('game.move');
    Route::post('/partie/{uuid}/abandonner', [GameController::class, 'resign'])->name('game.resign');
    Route::post('/partie/{uuid}/nulle', [GameController::class, 'offerDraw'])->name('game.draw');
    Route::get('/partie/{uuid}/etat', [GameController::class, 'getState'])->name('game.state');

    // Historique et replay
    Route::get('/mes-parties', [GameController::class, 'history'])->name('game.history');
    Route::get('/replay/{uuid}', [GameController::class, 'replay'])->name('game.replay');
});

/*
|--------------------------------------------------------------------------
| Routes Administration
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Thèmes (CRUD)
    Route::resource('themes', ThemeController::class);
    Route::post('/themes/{theme}/toggle-active', [ThemeController::class, 'toggleActive'])
        ->name('themes.toggle-active');

    // Cartes (CRUD)
    Route::resource('cards', CardController::class);
    Route::delete('/cards/{card}/image/{type}', [CardController::class, 'deleteImage'])
        ->name('cards.delete-image')
        ->where('type', 'main|evolution');
});
