import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins:[
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins:[],  // Bardzo wazne zeby przez bootstrapa nie wypierdalalo bledu. Breeze uzywa Tailwinda, pusta tablica dla pliku postcss powoduje ze wylacza to wszystkie wtyczki postcss w tym tailwind ktorego i tak juz nie ma
        },
    },
});