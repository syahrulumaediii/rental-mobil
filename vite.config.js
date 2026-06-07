import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth/login.css',
                'resources/js/auth/login.js',
                'resources/js/transaksi/pengembalian.js',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        host: '0.0.0.0', // Membuka akses Vite ke jaringan lokal
        hmr: {
            host: '192.168.1.12', // Tetap gunakan localhost untuk Hot Module Replacement di PC
        },
    },

});
