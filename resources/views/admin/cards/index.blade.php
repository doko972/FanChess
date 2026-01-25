<x-admin-layout>
    <x-slot name="header">Gestion des Cartes</x-slot>

    <!-- Filtres -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="theme_id" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                <option value="">Tous les thèmes</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->id }}" {{ request('theme_id') == $theme->id ? 'selected' : '' }}>
                        {{ $theme->name }}
                    </option>
                @endforeach
            </select>
            <select name="piece_type" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                <option value="">Tous les types</option>
                @foreach(\App\Models\Card::PIECE_TYPES as $type => $name)
                    <option value="{{ $type }}" {{ request('piece_type') == $type ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <select name="color" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                <option value="">Toutes les couleurs</option>
                <option value="white" {{ request('color') == 'white' ? 'selected' : '' }}>Blanc</option>
                <option value="black" {{ request('color') == 'black' ? 'selected' : '' }}>Noir</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 rounded-lg transition">
                Filtrer
            </button>
            @if(request()->hasAny(['theme_id', 'piece_type', 'color']))
                <a href="{{ route('admin.cards.index') }}" class="px-4 py-2 text-gray-400 hover:text-white">
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Grille des cartes -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        @forelse($cards as $card)
            <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-indigo-500 transition group">
                <div class="aspect-square relative {{ $card->color === 'white' ? 'bg-gradient-to-br from-gray-100 to-gray-200' : 'bg-gradient-to-br from-gray-700 to-gray-800' }}">
                    @if($card->image)
                        <img src="{{ Storage::url($card->image) }}" alt="{{ $card->name }}"
                             class="w-full h-full object-contain p-2">
                    @else
                        <div class="w-full h-full flex items-center justify-center {{ $card->color === 'white' ? 'bg-gray-200 text-gray-800' : 'bg-gray-700 text-gray-300' }}">
                            @php
                                $icons = [
                                    'king' => $card->color === 'white' ? '♔' : '♚',
                                    'queen' => $card->color === 'white' ? '♕' : '♛',
                                    'rook' => $card->color === 'white' ? '♖' : '♜',
                                    'bishop' => $card->color === 'white' ? '♗' : '♝',
                                    'knight' => $card->color === 'white' ? '♘' : '♞',
                                    'pawn' => $card->color === 'white' ? '♙' : '♟',
                                ];
                            @endphp
                            <span class="text-5xl">{{ $icons[$card->piece_type] ?? '?' }}</span>
                        </div>
                    @endif
                    <!-- Overlay actions -->
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center space-x-2">
                        <a href="{{ route('admin.cards.edit', $card) }}" 
                           class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition">
                            ✏️
                        </a>
                        <form action="{{ route('admin.cards.destroy', $card) }}" method="POST"
                              onsubmit="return confirm('Supprimer cette carte ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-500/30 rounded-lg hover:bg-red-500/50 transition">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-3">
                    <div class="font-medium text-sm truncate">{{ $card->name }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $card->theme->name }} • {{ $card->piece_type_name }}
                    </div>
                    <div class="flex items-center mt-1">
                        <span class="w-3 h-3 rounded-full mr-1 {{ $card->color === 'white' ? 'bg-gray-200' : 'bg-gray-600' }}"></span>
                        <span class="text-xs text-gray-400">{{ $card->color_name }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                Aucune carte trouvée.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $cards->withQueryString()->links() }}
    </div>
</x-admin-layout>
