<x-app-layout>
    <div class="min-h-[calc(100vh-8rem)]">
        <!-- Hero Section -->
        <section class="relative py-20 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/5 border border-white/10 mb-8">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></span>
                        <span class="text-sm text-gray-300">Nouveau : Thème Saint Seiya disponible !</span>
                    </div>

                    <!-- Titre -->
                    <h1 class="font-gaming text-5xl md:text-7xl font-bold mb-6">
                        <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Les Échecs
                        </span>
                        <br>
                        <span class="text-white">Réinventés</span>
                    </h1>

                    <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-10">
                        Jouez aux échecs avec vos personnages préférés. 
                        Saint Seiya, Seigneur des Anneaux, Stargate... 
                        Chaque pièce devient une carte de collection !
                    </p>

                    <!-- CTA -->
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('register') }}" class="btn-accent px-8 py-4 rounded-xl font-gaming font-bold text-lg text-black">
                            Commencer à jouer
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl font-gaming font-bold text-lg border border-white/20 hover:bg-white/5 transition">
                            J'ai déjà un compte
                        </a>
                    </div>
                </div>

                <!-- Preview Cards -->
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    @foreach(['Athéna' => '♔', 'Seiya' => '♞', 'Shaka' => '♗', 'Saga' => '♕'] as $name => $piece)
                    <div class="card-glass rounded-2xl p-4 text-center transform hover:scale-105 transition-transform duration-300 hover:glow">
                        <div class="text-5xl mb-3">{{ $piece }}</div>
                        <div class="font-gaming text-sm text-amber-400">{{ $name }}</div>
                        <div class="text-xs text-gray-500">Saint Seiya</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-black/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="font-gaming text-3xl md:text-4xl font-bold text-center mb-16">
                    <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                        Fonctionnalités
                    </span>
                </h2>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="card-glass rounded-2xl p-8 hover:glow transition-shadow duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-2xl">🎴</span>
                        </div>
                        <h3 class="font-gaming text-xl mb-3">Cartes Personnages</h3>
                        <p class="text-gray-400">
                            Chaque pièce est une carte avec l'illustration de votre personnage favori, son nom et une citation culte.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="card-glass rounded-2xl p-8 hover:glow transition-shadow duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-2xl">👥</span>
                        </div>
                        <h3 class="font-gaming text-xl mb-3">Multijoueur</h3>
                        <p class="text-gray-400">
                            Affrontez des joueurs du monde entier en temps réel. Timer optionnel pour des parties rapides ou sans limite.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="card-glass rounded-2xl p-8 hover:glow transition-shadow duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-2xl">🤖</span>
                        </div>
                        <h3 class="font-gaming text-xl mb-3">Jouer contre l'IA</h3>
                        <p class="text-gray-400">
                            Entraînez-vous contre Stockfish, l'un des moteurs d'échecs les plus puissants. Plusieurs niveaux de difficulté.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Themes Preview -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="font-gaming text-3xl md:text-4xl font-bold text-center mb-4">
                    <span class="bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent">
                        Thèmes Disponibles
                    </span>
                </h2>
                <p class="text-gray-400 text-center mb-16 max-w-xl mx-auto">
                    Choisissez votre univers préféré. Chaque thème offre une expérience unique avec des personnages iconiques.
                </p>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Saint Seiya -->
                    <div class="relative group overflow-hidden rounded-2xl">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-800 opacity-80"></div>
                        <div class="relative p-8">
                            <div class="text-5xl mb-4">⚔️</div>
                            <h3 class="font-gaming text-2xl mb-2">Saint Seiya</h3>
                            <p class="text-gray-300 text-sm mb-4">Les Chevaliers du Zodiaque</p>
                            <span class="inline-block px-3 py-1 bg-green-500/20 text-green-400 text-xs rounded-full">
                                Disponible
                            </span>
                        </div>
                    </div>

                    <!-- Seigneur des Anneaux -->
                    <div class="relative group overflow-hidden rounded-2xl opacity-60">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-700 to-amber-800 opacity-80"></div>
                        <div class="relative p-8">
                            <div class="text-5xl mb-4">🧙</div>
                            <h3 class="font-gaming text-2xl mb-2">Seigneur des Anneaux</h3>
                            <p class="text-gray-300 text-sm mb-4">La Terre du Milieu</p>
                            <span class="inline-block px-3 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full">
                                Bientôt
                            </span>
                        </div>
                    </div>

                    <!-- Plus à venir -->
                    <div class="relative group overflow-hidden rounded-2xl border-2 border-dashed border-white/20">
                        <div class="relative p-8 text-center">
                            <div class="text-5xl mb-4 opacity-50">➕</div>
                            <h3 class="font-gaming text-2xl mb-2 text-gray-500">Et plus encore...</h3>
                            <p class="text-gray-500 text-sm">
                                Stargate, Albator, Dragon Ball...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="py-20 bg-gradient-to-r from-indigo-900/50 to-purple-900/50">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <h2 class="font-gaming text-4xl md:text-5xl font-bold mb-6">
                    Prêt à jouer ?
                </h2>
                <p class="text-xl text-gray-400 mb-10">
                    Rejoignez la communauté FanChess et affrontez des joueurs du monde entier !
                </p>
                <a href="{{ route('register') }}" class="btn-accent px-10 py-5 rounded-xl font-gaming font-bold text-xl text-black inline-block">
                    Créer mon compte gratuitement
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
