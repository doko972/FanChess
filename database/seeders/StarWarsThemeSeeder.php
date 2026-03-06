<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class StarWarsThemeSeeder extends Seeder
{
    /**
     * Seed the Star Wars theme.
     * Blancs = Rebelles, Noirs = Empire
     */
    public function run(): void
    {
        // Créer le thème Star Wars
        $theme = Theme::create([
            'name' => 'Star Wars',
            'slug' => 'star-wars',
            'description' => 'L\'Alliance Rebelle affronte l\'Empire Galactique dans une bataille épique !',
            'primary_color' => '#FFE81F', // Jaune Star Wars
            'secondary_color' => '#000000', // Noir
            'accent_color' => '#C0C0C0', // Argent
            'is_active' => true,
            'is_premium' => false,
            'sort_order' => 10,
        ]);

        // ========================================
        // REBELLES (Blancs)
        // ========================================

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'king',
            'color' => 'white',
            'name' => 'Luke Skywalker',
            'description' => 'Le dernier espoir de la galaxie, héros de l\'Alliance Rebelle et Jedi légendaire.',
            'quote' => 'Je suis un Jedi, comme mon père avant moi.',
            'attack_visual' => 85,
            'defense_visual' => 70,
            'speed_visual' => 80,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'queen',
            'color' => 'white',
            'name' => 'Leia Organa',
            'description' => 'Princesse d\'Alderaan, générale de la Résistance et leader indomptable.',
            'quote' => 'L\'espoir est comme le soleil. Si vous n\'y croyez que quand vous le voyez, vous ne passerez jamais la nuit.',
            'attack_visual' => 75,
            'defense_visual' => 80,
            'speed_visual' => 85,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'rook',
            'color' => 'white',
            'name' => 'Chewbacca',
            'description' => 'Fidèle Wookiee, co-pilote du Faucon Millenium et guerrier redoutable.',
            'quote' => 'RRWWWGG! (Traduction : Personne ne touche à mes amis)',
            'attack_visual' => 90,
            'defense_visual' => 85,
            'speed_visual' => 60,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'bishop',
            'color' => 'white',
            'name' => 'Obi-Wan Kenobi',
            'description' => 'Maître Jedi légendaire, mentor de Luke et gardien de l\'espoir.',
            'quote' => 'Que la Force soit avec toi.',
            'attack_visual' => 80,
            'defense_visual' => 75,
            'speed_visual' => 70,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'knight',
            'color' => 'white',
            'name' => 'Han Solo',
            'description' => 'Contrebandier au grand coeur, capitaine du Faucon Millenium.',
            'quote' => 'Ne me dis jamais les probabilités !',
            'attack_visual' => 75,
            'defense_visual' => 65,
            'speed_visual' => 95,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'pawn',
            'color' => 'white',
            'name' => 'Soldat Rebelle',
            'description' => 'Courageux combattant de l\'Alliance, prêt à tout sacrifier pour la liberté.',
            'quote' => 'Pour l\'Alliance !',
            'attack_visual' => 50,
            'defense_visual' => 55,
            'speed_visual' => 60,
            'is_active' => true,
        ]);

        // ========================================
        // EMPIRE (Noirs)
        // ========================================

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'king',
            'color' => 'black',
            'name' => 'Dark Vador',
            'description' => 'Seigneur Sith, bras droit de l\'Empereur et terreur de la galaxie.',
            'quote' => 'Je suis ton père.',
            'attack_visual' => 95,
            'defense_visual' => 85,
            'speed_visual' => 70,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'queen',
            'color' => 'black',
            'name' => 'Empereur Palpatine',
            'description' => 'Maître Sith suprême, dirigeant tyrannique de l\'Empire Galactique.',
            'quote' => 'Le côté obscur de la Force est un chemin vers de nombreux pouvoirs...',
            'attack_visual' => 90,
            'defense_visual' => 70,
            'speed_visual' => 65,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'rook',
            'color' => 'black',
            'name' => 'AT-AT',
            'description' => 'Transport blindé tout terrain, forteresse mobile de l\'Empire.',
            'quote' => 'Puissance de feu maximale.',
            'attack_visual' => 95,
            'defense_visual' => 95,
            'speed_visual' => 30,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'bishop',
            'color' => 'black',
            'name' => 'Grand Moff Tarkin',
            'description' => 'Commandant impitoyable de l\'Étoile de la Mort, stratège impérial.',
            'quote' => 'La peur maintiendra les systèmes locaux dans le rang.',
            'attack_visual' => 70,
            'defense_visual' => 60,
            'speed_visual' => 75,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'knight',
            'color' => 'black',
            'name' => 'Boba Fett',
            'description' => 'Chasseur de primes légendaire, craint dans toute la galaxie.',
            'quote' => 'Il ne m\'est d\'aucune utilité s\'il est mort.',
            'attack_visual' => 85,
            'defense_visual' => 75,
            'speed_visual' => 90,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'pawn',
            'color' => 'black',
            'name' => 'Stormtrooper',
            'description' => 'Soldat d\'élite de l\'Empire, symbole de l\'ordre impérial.',
            'quote' => 'Ce ne sont pas les droïdes que vous recherchez... Attendez, si !',
            'attack_visual' => 45,
            'defense_visual' => 60,
            'speed_visual' => 55,
            'is_active' => true,
        ]);

        $this->command->info('Thème Star Wars créé avec succès !');
        $this->command->info('- 6 cartes Rebelles (blancs) : Luke, Leia, Chewbacca, Obi-Wan, Han Solo, Soldat Rebelle');
        $this->command->info('- 6 cartes Empire (noirs) : Dark Vador, Palpatine, AT-AT, Tarkin, Boba Fett, Stormtrooper');
    }
}
