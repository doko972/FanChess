<x-admin-layout>
    <x-slot name="header">{{ $theme->name }}</x-slot>

    <!-- Header avec infos thème -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-xl" style="background: linear-gradient(135deg, {{ $theme->primary_color }}, {{ $theme->secondary_color }})"></div>
                <div>
                    <h2 class="text-xl font-bold">{{ $theme->name }}</h2>
                    <p class="text-gray-400 text-sm mt-1">{{ $theme->description ?? 'Aucune description' }}</p>
                    <div class="flex items-center space-x-3 mt-2">
                        @if($theme->is_active)
                            <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs">Actif</span>
                        @else
                            <span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs">Inactif</span>
                        @endif
                        <span class="text-gray-500 text-sm">{{ $theme->cards->count() }}/12 cartes</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.themes.edit', $theme) }}" 
                   class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition">
                    Modifier
                </a>
                <a href="{{ route('admin.cards.create', ['theme_id' => $theme->id]) }}" 
                   class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 rounded-lg transition">
                    + Ajouter une carte
                </a>
            </div>
        </div>
    </div>

    <!-- Grille des cartes -->
    @foreach(['white' => 'Blancs (Protagonistes)', 'black' => 'Noirs (Antagonistes)'] as $color => $colorLabel)
    <div class="mb-8">
        <h3 class="font-gaming text-lg mb-4 flex items-center">
            <span class="w-4 h-4 rounded mr-2 {{ $color === 'white' ? 'bg-gray-200' : 'bg-gray-800 border border-gray-600' }}"></span>
            {{ $colorLabel }}
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach(['king', 'queen', 'rook', 'bishop', 'knight', 'pawn'] as $pieceType)
                @php
                    $card = $cardsByColor[$color][$pieceType] ?? null;
                    $pieceIcons = [
                        'king' => $color === 'white' ? '♔' : '♚',
                        'queen' => $color === 'white' ? '♕' : '♛',
                        'rook' => $color === 'white' ? '♖' : '♜',
                        'bishop' => $color === 'white' ? '♗' : '♝',
                        'knight' => $color === 'white' ? '♘' : '♞',
                        'pawn' => $color === 'white' ? '♙' : '♟',
                    ];
                    $pieceNames = \App\Models\Card::PIECE_TYPES;
                @endphp
                
                @if($card)
                    <!-- Carte existante -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-indigo-500 transition group">
                        <div class="aspect-square relative">
                            @if($card->image)
                                <img src="{{ Storage::url($card->image) }}" alt="{{ $card->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br {{ $color === 'white' ? 'from-gray-200 to-gray-300 text-gray-800' : 'from-gray-700 to-gray-800 text-gray-300' }}">
                                    <span class="text-5xl">{{ $pieceIcons[$pieceType] }}</span>
                                </div>
                            @endif
                            <!-- Overlay actions -->
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.cards.edit', $card) }}" 
                                   class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition" title="Modifier">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.cards.destroy', $card) }}" method="POST" 
                                      onsubmit="return confirm('Supprimer cette carte ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-500/30 rounded-lg hover:bg-red-500/50 transition" title="Supprimer">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="font-medium text-sm truncate">{{ $card->name }}</div>
                            <div class="text-xs text-gray-500">{{ $pieceNames[$pieceType] }}</div>
                        </div>
                    </div>
                @else
                    <!-- Carte manquante -->
                    <a href="{{ route('admin.cards.create', ['theme_id' => $theme->id, 'piece_type' => $pieceType, 'color' => $color]) }}"
                       class="bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-700 hover:border-indigo-500 transition flex flex-col items-center justify-center aspect-square p-4 group">
                        <span class="text-4xl opacity-30 group-hover:opacity-50 transition">{{ $pieceIcons[$pieceType] }}</span>
                        <span class="text-xs text-gray-500 mt-2">{{ $pieceNames[$pieceType] }}</span>
                        <span class="text-xs text-indigo-400 mt-1 opacity-0 group-hover:opacity-100 transition">+ Ajouter</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
    @endforeach

    <!-- Retour -->
    <div class="mt-8">
        <a href="{{ route('admin.themes.index') }}" class="text-gray-400 hover:text-white transition">
            ← Retour à la liste des thèmes
        </a>
    </div>
</x-admin-layout>
