<x-admin-layout>
    <x-slot name="header">Modifier : {{ $theme->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <form action="{{ route('admin.themes.update', $theme) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Nom du thème *
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $theme->name) }}" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $theme->description) }}</textarea>
                </div>

                <!-- Couleurs -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-300 mb-2">
                            Couleur primaire *
                        </label>
                        <div class="flex">
                            <input type="color" name="primary_color" id="primary_color" 
                                   value="{{ old('primary_color', $theme->primary_color) }}"
                                   class="w-12 h-12 rounded-l-lg border-0 cursor-pointer">
                            <input type="text" value="{{ old('primary_color', $theme->primary_color) }}" 
                                   class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-r-lg text-white text-sm"
                                   onchange="document.getElementById('primary_color').value = this.value">
                        </div>
                    </div>
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-300 mb-2">
                            Couleur secondaire *
                        </label>
                        <div class="flex">
                            <input type="color" name="secondary_color" id="secondary_color" 
                                   value="{{ old('secondary_color', $theme->secondary_color) }}"
                                   class="w-12 h-12 rounded-l-lg border-0 cursor-pointer">
                            <input type="text" value="{{ old('secondary_color', $theme->secondary_color) }}" 
                                   class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-r-lg text-white text-sm"
                                   onchange="document.getElementById('secondary_color').value = this.value">
                        </div>
                    </div>
                    <div>
                        <label for="accent_color" class="block text-sm font-medium text-gray-300 mb-2">
                            Couleur d'accent *
                        </label>
                        <div class="flex">
                            <input type="color" name="accent_color" id="accent_color" 
                                   value="{{ old('accent_color', $theme->accent_color) }}"
                                   class="w-12 h-12 rounded-l-lg border-0 cursor-pointer">
                            <input type="text" value="{{ old('accent_color', $theme->accent_color) }}" 
                                   class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-r-lg text-white text-sm"
                                   onchange="document.getElementById('accent_color').value = this.value">
                        </div>
                    </div>
                </div>

                <!-- Image de fond actuelle -->
                @if($theme->background_image)
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Image de fond actuelle</label>
                    <div class="relative inline-block">
                        <img src="{{ Storage::url($theme->background_image) }}" alt="Background" class="h-32 rounded-lg">
                    </div>
                </div>
                @endif

                <!-- Nouvelle image de fond -->
                <div>
                    <label for="background_image" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ $theme->background_image ? 'Remplacer l\'image de fond' : 'Image de fond (optionnel)' }}
                    </label>
                    <input type="file" name="background_image" id="background_image" accept="image/*"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-500 file:text-white file:cursor-pointer">
                </div>

                <!-- Options -->
                <div class="flex space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $theme->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">Actif</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $theme->is_premium) ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">Premium</span>
                    </label>
                </div>

                <!-- Ordre -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">
                        Ordre d'affichage
                    </label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $theme->sort_order) }}" min="0"
                           class="w-24 px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-700">
                    <a href="{{ route('admin.themes.index') }}" 
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
