<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CardController extends Controller
{
    /**
     * Liste toutes les cartes
     */
    public function index(Request $request)
    {
        $query = Card::with('theme');

        // Filtrer par thème
        if ($request->filled('theme_id')) {
            $query->where('theme_id', $request->theme_id);
        }

        // Filtrer par type de pièce
        if ($request->filled('piece_type')) {
            $query->where('piece_type', $request->piece_type);
        }

        // Filtrer par couleur
        if ($request->filled('color')) {
            $query->where('color', $request->color);
        }

        $cards = $query->orderBy('theme_id')
            ->orderBy('color')
            ->orderByRaw("FIELD(piece_type, 'king', 'queen', 'rook', 'bishop', 'knight', 'pawn')")
            ->paginate(12);

        $themes = Theme::orderBy('name')->get();

        return view('admin.cards.index', compact('cards', 'themes'));
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        $themes = Theme::active()->orderBy('name')->get();
        $selectedTheme = $request->filled('theme_id') ? Theme::find($request->theme_id) : null;

        return view('admin.cards.create', compact('themes', 'selectedTheme'));
    }

    /**
     * Enregistre une nouvelle carte
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:themes,id',
            'piece_type' => ['required', Rule::in(array_keys(Card::PIECE_TYPES))],
            'color' => ['required', Rule::in(array_keys(Card::COLORS))],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quote' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'image_evolution' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'attack_visual' => 'integer|min:0|max:100',
            'defense_visual' => 'integer|min:0|max:100',
            'speed_visual' => 'integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Vérifier unicité thème + type + couleur
        $exists = Card::where('theme_id', $validated['theme_id'])
            ->where('piece_type', $validated['piece_type'])
            ->where('color', $validated['color'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['piece_type' => 'Une carte existe déjà pour ce type de pièce et cette couleur dans ce thème.']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        // Upload images
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('cards', 'public');
        }

        if ($request->hasFile('image_evolution')) {
            $validated['image_evolution'] = $request->file('image_evolution')
                ->store('cards', 'public');
        }

        Card::create($validated);

        $theme = Theme::find($validated['theme_id']);

        return redirect()
            ->route('admin.themes.show', $theme)
            ->with('success', "La carte \"{$validated['name']}\" a été créée avec succès.");
    }

    /**
     * Affiche une carte
     */
    public function show(Card $card)
    {
        $card->load('theme');
        return view('admin.cards.show', compact('card'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Card $card)
    {
        $themes = Theme::active()->orderBy('name')->get();
        return view('admin.cards.edit', compact('card', 'themes'));
    }

    /**
     * Met à jour une carte
     */
    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:themes,id',
            'piece_type' => ['required', Rule::in(array_keys(Card::PIECE_TYPES))],
            'color' => ['required', Rule::in(array_keys(Card::COLORS))],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quote' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'image_evolution' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'attack_visual' => 'nullable|integer|min:0|max:100',
            'defense_visual' => 'nullable|integer|min:0|max:100',
            'speed_visual' => 'nullable|integer|min:0|max:100',
        ]);

        // Gérer le boolean is_active (checkbox non cochée = pas envoyée)
        $validated['is_active'] = $request->boolean('is_active');
        
        // Valeurs par défaut pour les stats si non renseignées
        $validated['attack_visual'] = $validated['attack_visual'] ?? $card->attack_visual ?? 50;
        $validated['defense_visual'] = $validated['defense_visual'] ?? $card->defense_visual ?? 50;
        $validated['speed_visual'] = $validated['speed_visual'] ?? $card->speed_visual ?? 50;

        // Vérifier unicité (sauf pour la carte actuelle)
        $exists = Card::where('theme_id', $validated['theme_id'])
            ->where('piece_type', $validated['piece_type'])
            ->where('color', $validated['color'])
            ->where('id', '!=', $card->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['piece_type' => 'Une carte existe déjà pour ce type de pièce et cette couleur dans ce thème.']);
        }

        // Upload nouvelle image principale
        if ($request->hasFile('image')) {
            if ($card->image) {
                Storage::disk('public')->delete($card->image);
            }
            $validated['image'] = $request->file('image')
                ->store('cards', 'public');
        }

        // Upload nouvelle image évolution
        if ($request->hasFile('image_evolution')) {
            if ($card->image_evolution) {
                Storage::disk('public')->delete($card->image_evolution);
            }
            $validated['image_evolution'] = $request->file('image_evolution')
                ->store('cards', 'public');
        }

        $card->update($validated);

        return redirect()
            ->route('admin.themes.show', $card->theme)
            ->with('success', "La carte \"{$card->name}\" a été mise à jour.");
    }

    /**
     * Supprime une carte
     */
    public function destroy(Card $card)
    {
        $cardName = $card->name;
        $theme = $card->theme;

        // Supprimer les images
        if ($card->image) {
            Storage::disk('public')->delete($card->image);
        }
        if ($card->image_evolution) {
            Storage::disk('public')->delete($card->image_evolution);
        }

        $card->delete();

        return redirect()
            ->route('admin.themes.show', $theme)
            ->with('success', "La carte \"{$cardName}\" a été supprimée.");
    }

    /**
     * Supprime l'image d'une carte
     */
    public function deleteImage(Card $card, string $type)
    {
        if ($type === 'main' && $card->image) {
            Storage::disk('public')->delete($card->image);
            $card->update(['image' => null]);
        } elseif ($type === 'evolution' && $card->image_evolution) {
            Storage::disk('public')->delete($card->image_evolution);
            $card->update(['image_evolution' => null]);
        }

        return back()->with('success', 'Image supprimée.');
    }
}