<x-admin-layout>
    <x-slot name="header">Gestion des Thèmes</x-slot>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <p class="text-gray-400">Gérez les familles de thèmes pour les échiquiers</p>
        <a href="{{ route('admin.themes.create') }}" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 rounded-lg transition flex items-center">
            <span class="mr-2">+</span> Nouveau Thème
        </a>
    </div>

    <!-- Liste -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-400 text-sm bg-gray-900/50">
                    <th class="p-4">Thème</th>
                    <th class="p-4">Couleurs</th>
                    <th class="p-4">Cartes</th>
                    <th class="p-4">Statut</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($themes as $theme)
                <tr class="border-t border-gray-700 hover:bg-gray-700/30 transition">
                    <td class="p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg" style="background: linear-gradient(135deg, {{ $theme->primary_color }}, {{ $theme->secondary_color }})"></div>
                            <div>
                                <div class="font-medium">{{ $theme->name }}</div>
                                <div class="text-xs text-gray-500">{{ $theme->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-1">
                            <div class="w-6 h-6 rounded" style="background: {{ $theme->primary_color }}" title="Primaire"></div>
                            <div class="w-6 h-6 rounded" style="background: {{ $theme->secondary_color }}" title="Secondaire"></div>
                            <div class="w-6 h-6 rounded" style="background: {{ $theme->accent_color }}" title="Accent"></div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="{{ $theme->cards_count >= 12 ? 'text-green-400' : 'text-amber-400' }}">
                            {{ $theme->cards_count }}/12
                        </span>
                        @if($theme->cards_count < 12)
                            <span class="text-xs text-gray-500 ml-1">(incomplet)</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($theme->is_active)
                            <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs">Actif</span>
                        @else
                            <span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs">Inactif</span>
                        @endif
                        @if($theme->is_premium)
                            <span class="px-2 py-1 bg-amber-500/20 text-amber-400 rounded-full text-xs ml-1">Premium</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.themes.show', $theme) }}" 
                               class="p-2 hover:bg-white/10 rounded-lg transition" title="Voir">
                                👁️
                            </a>
                            <a href="{{ route('admin.themes.edit', $theme) }}" 
                               class="p-2 hover:bg-white/10 rounded-lg transition" title="Modifier">
                                ✏️
                            </a>
                            <form action="{{ route('admin.themes.toggle-active', $theme) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 hover:bg-white/10 rounded-lg transition" 
                                        title="{{ $theme->is_active ? 'Désactiver' : 'Activer' }}">
                                    {{ $theme->is_active ? '🔒' : '🔓' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Supprimer ce thème et toutes ses cartes ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-500/20 rounded-lg transition text-red-400" title="Supprimer">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">
                        Aucun thème créé. 
                        <a href="{{ route('admin.themes.create') }}" class="text-indigo-400 hover:underline">Créer le premier</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $themes->links() }}
    </div>
</x-admin-layout>
