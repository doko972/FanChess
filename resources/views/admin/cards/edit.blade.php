<x-admin-layout>
    <x-slot name="header">Modifier : {{ $card->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <form action="{{ route('admin.cards.update', $card) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Thème -->
                <div>
                    <label for="theme_id" class="block text-sm font-medium text-gray-300 mb-2">
                        Thème *
                    </label>
                    <select name="theme_id" id="theme_id" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($themes as $theme)
                            <option value="{{ $theme->id }}" {{ old('theme_id', $card->theme_id) == $theme->id ? 'selected' : '' }}>
                                {{ $theme->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type de pièce et Couleur -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="piece_type" class="block text-sm font-medium text-gray-300 mb-2">
                            Type de pièce *
                        </label>
                        <select name="piece_type" id="piece_type" required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(\App\Models\Card::PIECE_TYPES as $type => $name)
                                <option value="{{ $type }}" {{ old('piece_type', $card->piece_type) == $type ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-300 mb-2">
                            Couleur (camp) *
                        </label>
                        <select name="color" id="color" required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="white" {{ old('color', $card->color) == 'white' ? 'selected' : '' }}>Blanc</option>
                            <option value="black" {{ old('color', $card->color) == 'black' ? 'selected' : '' }}>Noir</option>
                        </select>
                    </div>
                </div>

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Nom du personnage *
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $card->name) }}" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $card->description) }}</textarea>
                </div>

                <!-- Citation -->
                <div>
                    <label for="quote" class="block text-sm font-medium text-gray-300 mb-2">
                        Citation culte
                    </label>
                    <input type="text" name="quote" id="quote" value="{{ old('quote', $card->quote) }}"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Images actuelles -->
                <div class="grid grid-cols-2 gap-4">
                    @if($card->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Image actuelle</label>
                        <div class="relative inline-block bg-gray-700 rounded-lg">
                            <img src="{{ Storage::url($card->image) }}" alt="{{ $card->name }}" class="h-32 object-contain rounded-lg">
                            <form action="{{ route('admin.cards.delete-image', [$card, 'main']) }}" method="POST" class="absolute -top-2 -right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-6 h-6 bg-red-500 rounded-full text-white text-xs hover:bg-red-600"
                                        onclick="return confirm('Supprimer cette image ?')">×</button>
                            </form>
                        </div>
                    </div>
                    @endif
                    @if($card->image_evolution)
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Image évolution actuelle</label>
                        <div class="relative inline-block bg-gray-700 rounded-lg">
                            <img src="{{ Storage::url($card->image_evolution) }}" alt="Évolution" class="h-32 object-contain rounded-lg">
                            <form action="{{ route('admin.cards.delete-image', [$card, 'evolution']) }}" method="POST" class="absolute -top-2 -right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-6 h-6 bg-red-500 rounded-full text-white text-xs hover:bg-red-600"
                                        onclick="return confirm('Supprimer cette image ?')">×</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Nouvelles images -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ $card->image ? 'Remplacer l\'image' : 'Image principale' }}
                        </label>
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-500 file:text-white file:cursor-pointer file:text-sm">
                    </div>
                    <div>
                        <label for="image_evolution" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ $card->image_evolution ? 'Remplacer l\'évolution' : 'Image évolution' }}
                        </label>
                        <input type="file" name="image_evolution" id="image_evolution" accept="image/*"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-500 file:text-white file:cursor-pointer file:text-sm">
                    </div>
                </div>

                <!-- Stats visuelles -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">
                        Stats visuelles (décoratives)
                    </label>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="attack_visual" class="block text-xs text-gray-400 mb-1">Attaque</label>
                            <input type="range" name="attack_visual" id="attack_visual" min="0" max="100" 
                                   value="{{ old('attack_visual', $card->attack_visual) }}"
                                   class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer"
                                   oninput="document.getElementById('attack_value').textContent = this.value">
                            <span id="attack_value" class="text-xs text-red-400">{{ old('attack_visual', $card->attack_visual) }}</span>
                        </div>
                        <div>
                            <label for="defense_visual" class="block text-xs text-gray-400 mb-1">Défense</label>
                            <input type="range" name="defense_visual" id="defense_visual" min="0" max="100" 
                                   value="{{ old('defense_visual', $card->defense_visual) }}"
                                   class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer"
                                   oninput="document.getElementById('defense_value').textContent = this.value">
                            <span id="defense_value" class="text-xs text-blue-400">{{ old('defense_visual', $card->defense_visual) }}</span>
                        </div>
                        <div>
                            <label for="speed_visual" class="block text-xs text-gray-400 mb-1">Vitesse</label>
                            <input type="range" name="speed_visual" id="speed_visual" min="0" max="100" 
                                   value="{{ old('speed_visual', $card->speed_visual) }}"
                                   class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer"
                                   oninput="document.getElementById('speed_value').textContent = this.value">
                            <span id="speed_value" class="text-xs text-green-400">{{ old('speed_visual', $card->speed_visual) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actif -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $card->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">Carte active</span>
                    </label>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-700">
                    <a href="{{ route('admin.themes.show', $card->theme) }}" 
                       class="px-6 py-3 border border-gray-600 rounded-lg hover:bg-gray-700 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-indigo-500 hover:bg-indigo-600 rounded-lg transition">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
