import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
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
    // Configuración del servidor de desarrollo solo para pruebas en red local
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: '192.168.1.34' // Reemplaza con tu IP local
        }
    },
    // Optimizaciones para build en Railway
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', '@inertiajs/vue3'],
                    ui: ['primevue', '@fortawesome/vue-fontawesome'],
                    charts: ['chart.js']
                }
            }
        }
    }
});
