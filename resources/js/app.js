import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { Chess } from 'chess.js';

// Exposer Chess globalement pour les scripts inline
window.Chess = Chess;

// Plugins Alpine
Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();
