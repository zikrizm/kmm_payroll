import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/plugins/Select2/css/select2.css',
                'resources/plugins/Select2/js/select2.full.min.js',
                'resources/plugins/Toastr/toastr.css',
                'resources/plugins/Toastr/toastr.js',
                'resources/plugins/Dropzone/dropzone.css',
                'resources/plugins/Dropzone/dropzone.js',
                'resources/css/app.css',
                'resources/css/custom.css',
            ],
            refresh: true,
        }),
    ],
});
