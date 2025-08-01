const mix = require('laravel-mix');

require('mix-env-file');
mix.env(process.env.ENV_FILE);

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
    .scripts([
        'node_modules/prismjs/components/prism-core.js',
        'node_modules/prismjs/components/prism-clike.js',

        'node_modules/prismjs/components/prism-java.js',
        'node_modules/prismjs/components/prism-swift.js',
        'node_modules/prismjs/components/prism-python.js',
        'node_modules/prismjs/components/prism-markup.js',
        'node_modules/prismjs/components/prism-markup-templating.js',
        'node_modules/prismjs/components/prism-css.js',
        'node_modules/prismjs/components/prism-javascript.js',
        'node_modules/prismjs/components/prism-json.js',
        'node_modules/prismjs/components/prism-php.js',
        'node_modules/prismjs/components/prism-sql.js',
        'node_modules/prismjs/components/prism-yaml.js',

        'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.js',
        'node_modules/prismjs/plugins/autoloader/prism-autoloader.js',
    ], 'public/prismjs/prism.js')
    .sourceMaps(false, 'source-map');

mix.copy('node_modules/prismjs/themes/prism-coy.min.css', 'public/prismjs/');
mix.copy('node_modules/prismjs/themes/prism-tomorrow.min.css', 'public/prismjs/');
mix.copy('node_modules/prismjs/plugins/line-numbers/prism-line-numbers.min.css', 'public/prismjs/');

mix.copyDirectory('node_modules/tinymce/icons', 'public/tinymce/icons');
mix.copyDirectory('node_modules/tinymce/models', 'public/tinymce/models');
mix.copyDirectory('node_modules/tinymce/plugins', 'public/tinymce/plugins');
mix.copyDirectory('node_modules/tinymce/skins', 'public/tinymce/skins');
mix.copyDirectory('node_modules/tinymce/themes', 'public/tinymce/themes');
mix.copy('node_modules/tinymce/tinymce.min.js', 'public/tinymce/tinymce.min.js');
mix.copyDirectory('node_modules/tinymce-i18n/langs7', 'public/tinymce/langs');

mix.copy('node_modules/chart.js/dist/chart.umd.js', 'public/js/chart.umd.js');
mix.copy('node_modules/chart.js/dist/chart.umd.js.map', 'public/js/chart.umd.js.map');

mix.copy('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css', 'public/js/jquery.fancybox.min.css');
mix.copy('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js', 'public/js/jquery.fancybox.min.js');

mix.webpackConfig({
    stats: {
        children: false
    }
});
