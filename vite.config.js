import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue()
    ],
    server:{
        port: 5211, // vite port
        host: 'todo_app',          // app host
        origin: 'http://localhost:5211', // vite host
    },
});
