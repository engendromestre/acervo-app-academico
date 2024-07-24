import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const isDevelopment = mode === 'development';
    return {
        server: {
            https: isDevelopment ? false : true, // Usar HTTPS se não estiver em desenvolvimento
            hmr: {
                host: 'localhost',
                protocol: isDevelopment ? 'http' : 'https',
            },
        },
        plugins: [
            laravel({
                input: 'resources/js/app.js',
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
    };
});
