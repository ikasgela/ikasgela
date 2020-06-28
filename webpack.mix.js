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
    .sass('resources/sass/pdf.scss', 'public/css')
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

mix.copyDirectory('node_modules/tinymce/plugins', 'public/tinymce/plugins');
mix.copyDirectory('node_modules/tinymce/skins', 'public/tinymce/skins');
mix.copyDirectory('node_modules/tinymce/themes', 'public/tinymce/themes');
mix.copyDirectory('node_modules/tinymce/icons', 'public/tinymce/icons');
mix.copy('node_modules/tinymce/jquery.tinymce.min.js', 'public/tinymce/jquery.tinymce.min.js');
mix.copy('node_modules/tinymce/tinymce.min.js', 'public/tinymce/tinymce.min.js');
mix.copyDirectory('node_modules/tinymce-i18n/langs5', 'public/tinymce/langs');

mix.copy('node_modules/chart.js/dist/Chart.min.js', 'public/js/Chart.min.js');

mix.copy('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css', 'public/js/jquery.fancybox.min.css');
mix.copy('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js', 'public/js/jquery.fancybox.min.js');
