let mix = require('laravel-mix');
mix.setPublicPath('assets');

mix.setResourceRoot('../');
mix
    .js('src/js/boot.js', 'assets/js/boot.js')
    .js('src/js/main.js', 'assets/js/plugin-main-js-file.js')
    .copy('src/images', 'assets/images')
    .copy('src/js/game', 'assets/js/game')
    .copy('src/style.css', 'assets/css/style.css')
    .sass('src/scss/admin/app.scss', 'assets/css/element.css');

    