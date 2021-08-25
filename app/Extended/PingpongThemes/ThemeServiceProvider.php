<?php

namespace App\Extended\PingpongThemes;

use Pingpong\Themes\Finder;

class ThemeServiceProvider extends \Pingpong\Themes\ThemesServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app['themes'] = $this->app->share(function ($app) {
            return new Repository(
                new Finder(),
                $app['config'],
                $app['view'],
                $app['translator'],
                $app['cache.store']
            );
        });

        $this->registerCommands();

//        $this->overrideViewPath();
    }
}