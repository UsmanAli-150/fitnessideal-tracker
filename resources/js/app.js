import './bootstrap';

import Alpine from 'alpinejs';

import Chart from 'chart.js/auto';
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch((err) => {
            console.error('Service worker registration failed:', err);
        });
    });
}