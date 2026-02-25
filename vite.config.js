import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
           input: [
    'resources/css/app.css',
    'resources/css/web.css',
    'resources/css/inicio.css',
    'resources/css/contenido.css',
    'resources/css/nav.css',
    'resources/css/color.css',
    'resources/css/footer.css',
    'resources/js/app.js'
    
],
            refresh: true,
        }),
    ],
});
