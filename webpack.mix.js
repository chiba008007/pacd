const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/admin/admin.js', 'public/js/admin/admin.js')
    .js('resources/js/admin/event.js', 'public/js/admin/event.js')
    .js('resources/js/admin/pages.js', 'public/js/admin')
    .js('resources/js/admin/pages2.js', 'public/js/admin')
    .js('resources/js/admin/forms.js', 'public/js/admin')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/admin/admin.scss', 'public/css/admin.css')
    .copy('node_modules/tinymce/skins', 'public/js/admin/skins')
    .options({
        processCssUrls: false
    });
