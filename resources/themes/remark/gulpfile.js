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
    // LESS
    mix.less([
        'site.less',
        '../../../../../modules/Projects/Assets/less/workshop.less',
        '../../../../../modules/Users/Assets/less/workshop.less'
    ], elixir.config.publicPath + '/css/site.css');
    mix.less([
        'bootstrap.less',
        'bootstrap-extend.less'
    ], elixir.config.publicPath + '/css/bootstrap.extend.css');
    mix.less([
        'web-icons.less',
        'brand-icons.less',
        'font-awesome.less'
    ], elixir.config.publicPath + '/css/icons.css');
    // SCRIPTS
    mix.scripts('core.js', elixir.config.publicPath + '/js');
    mix.scripts('site.js', elixir.config.publicPath + '/js');
    mix.scripts([
        'sections/menu.js',
        'sections/menubar.js',
        'sections/gridmenu.js',
        'sections/sidebar.js'
    ], elixir.config.publicPath + '/js/sections.js');
    mix.scripts([
        'configs/config-colors.js',
        'configs/config-tour.js'
    ], elixir.config.publicPath + '/js/configs.js');
    mix.scripts([
        'components/asscrollable.js',
        'components/animsition.js',
        'components/slidepanel.js',
        'components/switchery.js',
        'components/panel.js',
        'components/toastr.js'
    ], elixir.config.publicPath + '/js/components.js');

    // VERSION
    //mix.version([
    //    'css/test.css'
    //]);

    // BROWSERSYNC
    //mix.browserSync({proxy: 'belsub.by'});
});
