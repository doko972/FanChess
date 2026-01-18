<x-app-layout>
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <div class="card-glass rounded-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 glow">
                        <span class="text-3xl">♔</span>
                    </div>
                    <h1 class="font-gaming text-2xl font-bold">Connexion</h1>
                    <p class="text-gray-400 mt-2">Bon retour parmi nous !</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-xl text-green-400 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

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
                               autofocus
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
                               autocomplete="current-password"
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="••••••••">
                        @error('password')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="remember" 
                                   class="w-4 h-4 rounded bg-white/5 border-white/20 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-400">Se souvenir de moi</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full btn-primary py-4 rounded-xl font-gaming font-bold text-lg">
                        Se connecter
                    </button>
                </form>

                <!-- Link to register -->
                <div class="mt-6 text-center">
                    <p class="text-gray-400">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 transition">
                            Inscrivez-vous
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
