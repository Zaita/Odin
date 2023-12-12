import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    build: {
      minify: "false",
      sourcemap: "true",
    },
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'resources/js/admin.jsx'],
            refresh: true,
        }),
        react(),
    ],
}); 
