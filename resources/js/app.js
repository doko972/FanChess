import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Plugins Alpine
Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();
