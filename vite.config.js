import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/prism.js',
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                // PrismJS themes and plugins
                {
                    src: 'node_modules/prismjs/themes/prism-coy.min.css',
                    dest: 'prismjs'
                },
                {
                    src: 'node_modules/prismjs/themes/prism-tomorrow.min.css',
                    dest: 'prismjs'
                },
                {
                    src: 'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.min.css',
                    dest: 'prismjs'
                },
                // TinyMCE
                {
                    src: 'node_modules/tinymce/icons',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce/models',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce/plugins',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce/skins',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce/themes',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce/tinymce.min.js',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce-i18n/langs7/*',
                    dest: 'tinymce/langs'
                },
                // Chart.js
                {
                    src: 'node_modules/chart.js/dist/chart.umd.js',
                    dest: 'js'
                },
                // Fancybox 5
                {
                    src: 'node_modules/@fancyapps/ui/dist/fancybox/fancybox.css',
                    dest: 'fancybox'
                },
                {
                    src: 'node_modules/@fancyapps/ui/dist/fancybox/fancybox.umd.js',
                    dest: 'fancybox'
                },
                // Single click script
                {
                    src: 'resources/js/single_click.js',
                    dest: 'js'
                },
            ]
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
