import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '172.16.88.22', // o '0.0.0.0' para todas las interfaces
        port: 5173,           // puerto por defecto de Vite
        hmr: {
            host: '172.16.88.22', // importante para HMR
        },
        cors: {
            origin: ['http://172.16.88.22', 'http://172.16.88.22:8000', 'http://localhost'],
            credentials: true,
        },
    },
});
