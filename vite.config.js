import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    preview: {
        host: true,
        port: 4173, // You can leave this or set it as needed
        allowedHosts: ['warehouse-2-7l05.onrender.com'],
    },
});
