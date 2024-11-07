const mix = require('laravel-mix');

mix.setPublicPath('public') // Asegúrate de que esta línea esté presente y correcta

mix.js('resources/js/main.js', 'public/js')
   .vue()
   .sass('resources/sass/app.scss', 'public/css')
   .version();
