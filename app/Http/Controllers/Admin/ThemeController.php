<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThemeController extends Controller
{
    /**
     * Liste tous les thèmes
     */
    public function index()
    {
        $themes = Theme::withCount('cards')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.themes.index', compact('themes'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.themes.create');
    }

    /**
     * Enregistre un nouveau thème
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:themes',
            'description' => 'nullable|string|max:1000',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'music_file' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
            'sound_move' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_capture' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_check' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_checkmate' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_victory' => 'nullable|file|mimes:mp3,wav,ogg|max:2048',
            'sound_defeat' => 'nullable|file|mimes:mp3,wav,ogg|max:2048',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_premium'] = $request->boolean('is_premium');

        // Upload image de fond si présente
        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')
                ->store('themes/backgrounds', 'public');
        }

        // Upload fichiers audio
        $audioFields = ['music_file', 'sound_move', 'sound_capture', 'sound_check', 'sound_checkmate', 'sound_victory', 'sound_defeat'];
        foreach ($audioFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('themes/sounds', 'public');
            }
        }

        Theme::create($validated);

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Le thème \"{$validated['name']}\" a été créé avec succès.");
    }

    /**
     * Affiche un thème
     */
    public function show(Theme $theme)
    {
        $theme->load('cards');
        
        // Organiser les cartes par couleur et type
        $cardsByColor = [
            'white' => $theme->cards->where('color', 'white')->keyBy('piece_type'),
            'black' => $theme->cards->where('color', 'black')->keyBy('piece_type'),
        ];

        return view('admin.themes.show', compact('theme', 'cardsByColor'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Theme $theme)
    {
        return view('admin.themes.edit', compact('theme'));
    }

    /**
     * Met à jour un thème
     */
    public function update(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:themes,name,' . $theme->id,
            'description' => 'nullable|string|max:1000',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'music_file' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
            'sound_move' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_capture' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_check' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_checkmate' => 'nullable|file|mimes:mp3,wav,ogg|max:1024',
            'sound_victory' => 'nullable|file|mimes:mp3,wav,ogg|max:2048',
            'sound_defeat' => 'nullable|file|mimes:mp3,wav,ogg|max:2048',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_premium'] = $request->boolean('is_premium');

        // Upload nouvelle image de fond
        if ($request->hasFile('background_image')) {
            // Supprimer l'ancienne image
            if ($theme->background_image) {
                Storage::disk('public')->delete($theme->background_image);
            }
            $validated['background_image'] = $request->file('background_image')
                ->store('themes/backgrounds', 'public');
        }

        // Upload fichiers audio
        $audioFields = ['music_file', 'sound_move', 'sound_capture', 'sound_check', 'sound_checkmate', 'sound_victory', 'sound_defeat'];
        foreach ($audioFields as $field) {
            if ($request->hasFile($field)) {
                // Supprimer l'ancien fichier
                if ($theme->$field) {
                    Storage::disk('public')->delete($theme->$field);
                }
                $validated[$field] = $request->file($field)->store('themes/sounds', 'public');
            }
        }

        $theme->update($validated);

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Le thème \"{$theme->name}\" a été mis à jour.");
    }

    /**
     * Supprime un thème
     */
    public function destroy(Theme $theme)
    {
        $themeName = $theme->name;

        // Supprimer l'image de fond
        if ($theme->background_image) {
            Storage::disk('public')->delete($theme->background_image);
        }

        // Supprimer les fichiers audio
        $audioFields = ['music_file', 'sound_move', 'sound_capture', 'sound_check', 'sound_checkmate', 'sound_victory', 'sound_defeat'];
        foreach ($audioFields as $field) {
            if ($theme->$field) {
                Storage::disk('public')->delete($theme->$field);
            }
        }

        // Supprimer les images des cartes
        foreach ($theme->cards as $card) {
            if ($card->image) {
                Storage::disk('public')->delete($card->image);
            }
            if ($card->image_evolution) {
                Storage::disk('public')->delete($card->image_evolution);
            }
        }

        $theme->delete();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Le thème \"{$themeName}\" a été supprimé.");
    }

    /**
     * Active/Désactive un thème
     */
    public function toggleActive(Theme $theme)
    {
        $theme->update(['is_active' => !$theme->is_active]);

        $status = $theme->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Le thème \"{$theme->name}\" a été {$status}.");
    }
}
