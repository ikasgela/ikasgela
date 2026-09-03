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
                    dest: 'prismjs',
                    rename: { stripBase: true }
                },
                {
                    src: 'node_modules/prismjs/themes/prism-tomorrow.min.css',
                    dest: 'prismjs',
                    rename: { stripBase: true }
                },
                {
                    src: 'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.min.css',
                    dest: 'prismjs',
                    rename: { stripBase: true }
                },
                // TinyMCE
                {
                    src: 'node_modules/tinymce/icons',
                    dest: 'tinymce',
                    rename: { stripBase: 2 }
                },
                {
                    src: 'node_modules/tinymce/models',
                    dest: 'tinymce',
                    rename: { stripBase: 2 }
                },
                {
                    src: 'node_modules/tinymce/plugins',
                    dest: 'tinymce',
                    rename: { stripBase: 2 }
                },
                {
                    src: 'node_modules/tinymce/skins',
                    dest: 'tinymce',
                    rename: { stripBase: 2 }
                },
                {
                    src: 'node_modules/tinymce/themes',
                    dest: 'tinymce',
                    rename: { stripBase: 2 }
                },
                {
                    src: 'node_modules/tinymce/tinymce.min.js',
                    dest: 'tinymce',
                    rename: { stripBase: 2 }
                },
                {
                    src: 'node_modules/tinymce-i18n/langs8',
                    dest: 'tinymce/langs',
                    rename: { stripBase: true }
                },
                // Chart.js
                {
                    src: 'node_modules/chart.js/dist/chart.umd.js',
                    dest: 'js',
                    rename: { stripBase: true }
                },
                // Fancybox 5
                {
                    src: 'node_modules/@fancyapps/ui/dist/fancybox/fancybox.css',
                    dest: 'fancybox',
                    rename: { stripBase: true }
                },
                {
                    src: 'node_modules/@fancyapps/ui/dist/fancybox/fancybox.umd.js',
                    dest: 'fancybox',
                    rename: { stripBase: true }
                },
                // Single click script
                {
                    src: 'resources/js/single_click.js',
                    dest: 'js',
                    rename: { stripBase: true }
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
