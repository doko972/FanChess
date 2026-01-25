<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class LordOfTheRingsThemeSeeder extends Seeder
{
    /**
     * Seed the Lord of the Rings theme.
     * Blancs = Peuples Libres, Noirs = Mordor
     */
    public function run(): void
    {
        // Créer le thème Le Seigneur des Anneaux
        $theme = Theme::create([
            'name' => 'Le Seigneur des Anneaux',
            'slug' => 'seigneur-des-anneaux',
            'description' => 'Les Peuples Libres de la Terre du Milieu affrontent les forces de Mordor !',
            'primary_color' => '#C9A227', // Or
            'secondary_color' => '#2C1810', // Brun foncé
            'accent_color' => '#228B22', // Vert forêt
            'is_active' => true,
            'is_premium' => false,
            'sort_order' => 20,
        ]);

        // ========================================
        // PEUPLES LIBRES (Blancs)
        // ========================================

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'king',
            'color' => 'white',
            'name' => 'Aragorn',
            'description' => 'Héritier d\'Isildur, Rôdeur du Nord et Roi du Gondor restauré.',
            'quote' => 'Je suis Aragorn, fils d\'Arathorn, et si par ma vie ou ma mort je puis vous sauver, je le ferai.',
            'attack_visual' => 90,
            'defense_visual' => 85,
            'speed_visual' => 80,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'queen',
            'color' => 'white',
            'name' => 'Arwen',
            'description' => 'Étoile du Soir, fille d\'Elrond et reine du Gondor.',
            'quote' => 'Je préfère partager une vie mortelle avec vous que d\'affronter seule tous les âges du monde.',
            'attack_visual' => 70,
            'defense_visual' => 75,
            'speed_visual' => 90,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'rook',
            'color' => 'white',
            'name' => 'Gimli',
            'description' => 'Seigneur des Cavernes Étincelantes, guerrier nain à la hache redoutable.',
            'quote' => 'Personne ne lance un Nain !',
            'attack_visual' => 95,
            'defense_visual' => 90,
            'speed_visual' => 45,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'bishop',
            'color' => 'white',
            'name' => 'Boromir',
            'description' => 'Fils de l\'Intendant du Gondor, valeureux capitaine de la Tour Blanche.',
            'quote' => 'On ne marche pas simplement vers le Mordor.',
            'attack_visual' => 85,
            'defense_visual' => 80,
            'speed_visual' => 70,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'knight',
            'color' => 'white',
            'name' => 'Théoden',
            'description' => 'Roi du Rohan, seigneur des Rohirrim et des chevaux.',
            'quote' => 'En avant Eorlingas !',
            'attack_visual' => 80,
            'defense_visual' => 75,
            'speed_visual' => 95,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'pawn',
            'color' => 'white',
            'name' => 'Soldat de la Tour',
            'description' => 'Fidèle garde du Gondor, défenseur de Minas Tirith.',
            'quote' => 'Pour le Gondor !',
            'attack_visual' => 55,
            'defense_visual' => 65,
            'speed_visual' => 50,
            'is_active' => true,
        ]);

        // ========================================
        // MORDOR (Noirs)
        // ========================================

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'king',
            'color' => 'black',
            'name' => 'Sauron',
            'description' => 'Le Seigneur Ténébreux, maître de l\'Anneau Unique et de Mordor.',
            'quote' => 'Il n\'y a pas de vie dans le vide, seulement la mort.',
            'attack_visual' => 100,
            'defense_visual' => 90,
            'speed_visual' => 60,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'queen',
            'color' => 'black',
            'name' => 'Roi-Sorcier d\'Angmar',
            'description' => 'Chef des Nazgûl, le plus terrible serviteur de Sauron.',
            'quote' => 'Aucun homme ne peut me tuer.',
            'attack_visual' => 95,
            'defense_visual' => 85,
            'speed_visual' => 80,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'rook',
            'color' => 'black',
            'name' => 'Troll des Cavernes',
            'description' => 'Créature massive et brutale des profondeurs de la Moria.',
            'quote' => 'GRAAAH!',
            'attack_visual' => 95,
            'defense_visual' => 95,
            'speed_visual' => 25,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'bishop',
            'color' => 'black',
            'name' => 'Saroumane',
            'description' => 'Le Magicien Blanc corrompu, traître à l\'Ordre des Istari.',
            'quote' => 'Contre le pouvoir du Mordor, il ne peut y avoir de victoire.',
            'attack_visual' => 85,
            'defense_visual' => 70,
            'speed_visual' => 65,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'knight',
            'color' => 'black',
            'name' => 'Nazgûl',
            'description' => 'Spectre de l\'Anneau, ancien roi des Hommes asservi à Sauron.',
            'quote' => 'La Comté... Sacquet...',
            'attack_visual' => 80,
            'defense_visual' => 70,
            'speed_visual' => 90,
            'is_active' => true,
        ]);

        Card::create([
            'theme_id' => $theme->id,
            'piece_type' => 'pawn',
            'color' => 'black',
            'name' => 'Orque',
            'description' => 'Créature corrompue du Mordor, soldat des armées de Sauron.',
            'quote' => 'On n\'est pas des elfes !',
            'attack_visual' => 50,
            'defense_visual' => 45,
            'speed_visual' => 60,
            'is_active' => true,
        ]);

        $this->command->info('Thème Le Seigneur des Anneaux créé avec succès !');
        $this->command->info('- 6 cartes Peuples Libres (blancs) : Aragorn, Arwen, Gimli, Boromir, Théoden, Soldat de la Tour');
        $this->command->info('- 6 cartes Mordor (noirs) : Sauron, Roi-Sorcier, Troll, Saroumane, Nazgûl, Orque');
    }
}
