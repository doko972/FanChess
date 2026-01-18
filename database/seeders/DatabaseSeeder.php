<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer l'administrateur
        $admin = User::create([
            'name' => 'Admin FanChess',
            'email' => 'admin@fanchess.local',
            'password' => Hash::make('Admin123!'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        // Créer un utilisateur de test
        User::create([
            'name' => 'Joueur Test',
            'email' => 'joueur@fanchess.local',
            'password' => Hash::make('Joueur123!'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        // Créer le thème Saint Seiya
        $saintSeiya = Theme::create([
            'name' => 'Saint Seiya',
            'slug' => 'saint-seiya',
            'description' => 'Les Chevaliers du Zodiaque protègent Athéna dans ce thème épique inspiré de l\'anime culte.',
            'primary_color' => '#1e3a8a', // Bleu foncé (cosmos)
            'secondary_color' => '#7c3aed', // Violet (armures)
            'accent_color' => '#fbbf24', // Or (armures d'or)
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Créer les cartes Saint Seiya - Camp Blanc (Athéna)
        $whiteCards = [
            ['piece_type' => 'king', 'name' => 'Athéna', 'description' => 'Déesse de la sagesse et protectrice de la Terre.', 'quote' => 'Je protégerai toujours la Terre et l\'humanité.', 'attack' => 60, 'defense' => 100, 'speed' => 50],
            ['piece_type' => 'queen', 'name' => 'Saga des Gémeaux', 'description' => 'Le plus puissant des Gold Saints, maître de l\'illusion.', 'quote' => 'Une autre dimension !', 'attack' => 95, 'defense' => 85, 'speed' => 90],
            ['piece_type' => 'rook', 'name' => 'Aldébaran du Taureau', 'description' => 'Le colosse gardien de la deuxième maison.', 'quote' => 'Great Horn !', 'attack' => 90, 'defense' => 95, 'speed' => 60],
            ['piece_type' => 'bishop', 'name' => 'Shaka de la Vierge', 'description' => 'L\'homme le plus proche des dieux.', 'quote' => 'Je vais t\'envoyer dans un des six mondes.', 'attack' => 98, 'defense' => 80, 'speed' => 85],
            ['piece_type' => 'knight', 'name' => 'Seiya de Pégase', 'description' => 'Le Bronze Saint au cœur inébranlable.', 'quote' => 'Je me relèverai toujours !', 'attack' => 80, 'defense' => 70, 'speed' => 95],
            ['piece_type' => 'pawn', 'name' => 'Soldat du Sanctuaire', 'description' => 'Garde fidèle du Sanctuaire d\'Athéna.', 'quote' => 'Pour Athéna !', 'attack' => 30, 'defense' => 40, 'speed' => 50],
        ];

        foreach ($whiteCards as $cardData) {
            Card::create([
                'theme_id' => $saintSeiya->id,
                'piece_type' => $cardData['piece_type'],
                'color' => 'white',
                'name' => $cardData['name'],
                'description' => $cardData['description'],
                'quote' => $cardData['quote'],
                'attack_visual' => $cardData['attack'],
                'defense_visual' => $cardData['defense'],
                'speed_visual' => $cardData['speed'],
                'is_active' => true,
            ]);
        }

        // Créer les cartes Saint Seiya - Camp Noir (Hadès)
        $blackCards = [
            ['piece_type' => 'king', 'name' => 'Hadès', 'description' => 'Dieu des Enfers et souverain du monde souterrain.', 'quote' => 'La mort est inévitable.', 'attack' => 70, 'defense' => 100, 'speed' => 60],
            ['piece_type' => 'queen', 'name' => 'Pandore', 'description' => 'Servante dévouée d\'Hadès et maîtresse des Spectres.', 'quote' => 'Hadès vaincra !', 'attack' => 85, 'defense' => 75, 'speed' => 90],
            ['piece_type' => 'rook', 'name' => 'Rhadamanthe de la Wyvern', 'description' => 'Le plus puissant des trois Juges.', 'quote' => 'Greatest Caution !', 'attack' => 92, 'defense' => 88, 'speed' => 80],
            ['piece_type' => 'bishop', 'name' => 'Minos du Griffon', 'description' => 'Juge des Enfers aux fils cosmiques.', 'quote' => 'Cosmic Marionation !', 'attack' => 88, 'defense' => 82, 'speed' => 85],
            ['piece_type' => 'knight', 'name' => 'Éaque du Garuda', 'description' => 'Le Juge vengeur aux ailes noires.', 'quote' => 'Garuda Flap !', 'attack' => 85, 'defense' => 78, 'speed' => 92],
            ['piece_type' => 'pawn', 'name' => 'Spectre d\'Hadès', 'description' => 'Guerrier des Enfers ressuscité.', 'quote' => 'Pour le Seigneur Hadès !', 'attack' => 35, 'defense' => 35, 'speed' => 55],
        ];

        foreach ($blackCards as $cardData) {
            Card::create([
                'theme_id' => $saintSeiya->id,
                'piece_type' => $cardData['piece_type'],
                'color' => 'black',
                'name' => $cardData['name'],
                'description' => $cardData['description'],
                'quote' => $cardData['quote'],
                'attack_visual' => $cardData['attack'],
                'defense_visual' => $cardData['defense'],
                'speed_visual' => $cardData['speed'],
                'is_active' => true,
            ]);
        }

        // Créer un thème vide pour démonstration (incomplet)
        Theme::create([
            'name' => 'Seigneur des Anneaux',
            'slug' => 'seigneur-des-anneaux',
            'description' => 'La Terre du Milieu s\'invite sur l\'échiquier. (En cours de création)',
            'primary_color' => '#166534',
            'secondary_color' => '#854d0e',
            'accent_color' => '#dc2626',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $this->command->info('✅ Base de données initialisée avec succès !');
        $this->command->info('');
        $this->command->info('📧 Compte Admin : admin@fanchess.local / Admin123!');
        $this->command->info('📧 Compte Joueur : joueur@fanchess.local / Joueur123!');
    }
}
