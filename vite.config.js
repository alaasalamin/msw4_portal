import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.tsx',
                'resources/js/admin-echo.js',
                'resources/js/text-block-preview.tsx',
            ],
            refresh: true,
        }),
        react(),
    ],
});
