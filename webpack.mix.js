const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .styles([
        'node_modules/prismjs/themes/prism-coy.css',
        'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.css',
    ], 'public/css/prism.css')
    .scripts([
        'node_modules/prismjs/components/prism-core.js',
        'node_modules/prismjs/components/prism-clike.js',

        'node_modules/prismjs/components/prism-java.js',
        'node_modules/prismjs/components/prism-swift.js',
        'node_modules/prismjs/components/prism-python.js',

        'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.js',
        'node_modules/prismjs/plugins/autoloader/prism-autoloader.js',
    ], 'public/js/prism.js');
