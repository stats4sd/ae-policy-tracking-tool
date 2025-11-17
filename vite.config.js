import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel, {refreshPaths} from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss({

        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                }
            }
        }),
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/assessment-app.ts',
                'resources/css/app.css',
                'resources/css/filament/app/theme.css',
                'resources/css/filament/admin/theme.css',
            ],
            refresh: [
                ...refreshPaths,
                'app/',
                'packages/',
                'resources/views/'
            ],
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        }
    }
})
