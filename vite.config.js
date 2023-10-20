import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/welcome.css',
                'resources/js/app.js',
                'resources/js/programs/form.js',
                'resources/js/models/form.js',
                'resources/js/programs/show.js',
                'resources/js/seasons/form.js',
                'resources/js/seasons/show.js',
                'resources/js/episodes/form.js',
                'resources/js/episodes/show.js',
                'resources/js/lives/form.js',
                'resources/js/lives/show.js',
                'resources/js/news/form.js',
                'resources/js/news/show.js',
            ],
            refresh: true,
        }),
    ],
});
