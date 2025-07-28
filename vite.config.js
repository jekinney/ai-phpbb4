import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    resolve: {
        alias: {
            // Ensure TinyMCE can find its assets
            'tinymce': resolve(__dirname, 'node_modules/tinymce'),
        }
    },
    optimizeDeps: {
        include: ['tinymce']
    }
});