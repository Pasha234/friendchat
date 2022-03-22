const mix = require('laravel-mix');
const path = require('path');

mix.alias({
    '@': path.resolve(__dirname, 'resources')
})
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
.vue()
.copyDirectory('resources/css', 'public/css')
.copyDirectory('resources/img', 'public/img')
// .sass('resources/sass/app.scss', 'public/css');
