<x-app-layout>
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <div class="card-glass rounded-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 glow">
                        <span class="text-3xl">♔</span>
                    </div>
                    <h1 class="font-gaming text-2xl font-bold">Rejoindre FanChess</h1>
                    <p class="text-gray-400 mt-2">Créez votre compte et commencez à jouer</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Honeypot - Champ invisible pour les bots -->
                    <div style="position: absolute; left: -9999px;" aria-hidden="true">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>
                    <!-- Timestamp pour détecter les soumissions trop rapides -->
                    <input type="hidden" name="form_timestamp" value="{{ time() }}">

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            Pseudo
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required 
                               autofocus
                               autocomplete="name"
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="Votre pseudo de joueur">
                        @error('name')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required 
                               autocomplete="email"
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="votre@email.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            Mot de passe
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="••••••••">
                        @error('password')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                            Confirmer le mot de passe
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="••••••••">
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full btn-primary py-4 rounded-xl font-gaming font-bold text-lg">
                        Créer mon compte
                    </button>
                </form>

                <!-- Link to login -->
                <div class="mt-6 text-center">
                    <p class="text-gray-400">
                        Déjà inscrit ?
                        <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition">
                            Connectez-vous
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
