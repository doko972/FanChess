# 🎮 FanChess - Jeu d'échecs thématique

Un jeu d'échecs en ligne avec des thèmes de franchises populaires (Saint Seiya, Seigneur des Anneaux, Stargate...). Chaque pièce est représentée par une carte de personnage.

## ✨ Fonctionnalités

- 🎴 **Système de cartes** : Chaque pièce est une carte avec illustration, nom et description
- 🎨 **Thèmes multiples** : Saint Seiya (inclus), extensible à d'autres franchises
- 👥 **Multijoueur temps réel** : Via WebSocket (Laravel Reverb)
- 🤖 **Jouer contre l'IA** : Stockfish.js intégré (plusieurs niveaux de difficulté)
- ⏱️ **Timer optionnel** : Mode Blitz, Rapid ou sans limite
- 🔒 **Sécurité renforcée** : Routes personnalisées, rate limiting, honeypot, CAPTCHA
- 👑 **Dashboard Admin** : Gestion des thèmes et cartes (CRUD complet)

## 🛠️ Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Backend | Laravel 11 |
| Auth | Laravel Breeze (modifié) |
| Base de données | MySQL |
| WebSocket | Laravel Reverb |
| Logique échecs | chess.js |
| IA | Stockfish.js (WASM) |
| Frontend | Blade + Alpine.js |
| CSS | Tailwind CSS |

## 📦 Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

### Étapes d'installation

```bash
# 1. Créer le projet Laravel
composer create-project laravel/laravel fanchess
cd fanchess

# 2. Installer les dépendances
composer require laravel/breeze --dev
php artisan breeze:install blade

# 3. Installer Laravel Reverb pour WebSocket
php artisan install:broadcasting

# 4. Installer les dépendances npm
npm install
npm install chess.js alpinejs

# 5. Configurer le .env
cp .env.example .env
php artisan key:generate

# 6. Configurer la base de données dans .env
# DB_DATABASE=fanchess
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Lancer les migrations et seeders
php artisan migrate
php artisan db:seed

# 8. Créer le lien symbolique pour le storage
php artisan storage:link

# 9. Compiler les assets
npm run build

# 10. Lancer le serveur
php artisan serve

# 11. Dans un autre terminal, lancer Reverb
php artisan reverb:start
```

## 🔐 Sécurité

Les routes d'authentification ont été personnalisées :
- `/register` → `/rejoindre`
- `/login` → `/connexion`
- `/logout` → `/deconnexion`

Protections activées :
- Rate limiting (5 tentatives/minute)
- Champ honeypot anti-bot
- CSRF token
- Vérification email obligatoire

## 👑 Accès Administrateur

Après le seeding, un compte admin est créé :
- **Email** : admin@fanchess.local
- **Mot de passe** : Admin123!

Dashboard admin : `/admin/dashboard`

## 🎴 Gestion des Thèmes

Dans le dashboard admin :
1. **Thèmes** : Créer une famille de thème (ex: Saint Seiya)
2. **Cartes** : Ajouter les 6 types de pièces pour chaque thème

Types de pièces :
- `king` → Roi
- `queen` → Dame
- `rook` → Tour
- `bishop` → Fou
- `knight` → Cavalier
- `pawn` → Pion

## 🎮 Jouer

1. Se connecter ou créer un compte
2. Aller dans le **Lobby**
3. Choisir son thème préféré
4. Créer une partie (vs Joueur ou vs IA)
5. Attendre un adversaire ou jouer contre l'IA

## 📁 Structure des fichiers

```
fanchess/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ThemeController.php
│   │   │   │   ├── CardController.php
│   │   │   │   └── DashboardController.php
│   │   │   └── Game/
│   │   │       ├── LobbyController.php
│   │   │       └── GameController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       └── HoneypotMiddleware.php
│   ├── Models/
│   │   ├── Theme.php
│   │   ├── Card.php
│   │   ├── Game.php
│   │   └── GameMove.php
│   └── Events/
│       ├── GameMove.php
│       └── GameCreated.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   ├── auth/
│   │   ├── game/
│   │   └── layouts/
│   ├── css/
│   └── js/
└── routes/
    ├── web.php
    └── channels.php
```

## 🚀 Évolutions futures

- [ ] Système de ranking ELO
- [ ] Chat en partie
- [ ] Historique des parties avec replay
- [ ] Boutique de thèmes
- [ ] Tournois
- [ ] Application mobile

## 📄 Licence

Projet personnel - Tous droits réservés

---

Développé avec ❤️ par L'Atelier Normand du Web
