var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    // LESS:REMARK
    mix.less('test.less', 'public/themes/remark/css');

    // LESS:GLOBAL
    //mix.less('app.less', 'public/global/css');

    // VERSION
    mix.version([
        'themes/remark/css/test.css',
        'global/css/app.css'
    ]);

    // BROWSERSYNC
    //mix.browserSync({proxy: 'belsub.by'});
});
