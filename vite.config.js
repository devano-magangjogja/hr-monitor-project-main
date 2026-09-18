import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            // Gunakan polling untuk menghindari error "notify file events failed" di Windows/WSL
            usePolling: true,
            interval: 1000,
            ignored: ['**/public/images/**', '**/public/storage/**', '**/node_modules/**', '**/.git/**'],
        },
    },
});