<?php

namespace Modules\Search\Providers;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerComposers();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
//        $this->publishes([
//            __DIR__.'/../Config/config.php' => config_path('search.php'),
//        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'search'
        );
    }

    /**
     * Register view composers
     *
     * @return void
     */
    public function registerComposers()
    {
        //Experimental
        $theme = theme()->getCurrent();
        view()->composer(
            "{$theme}::partials.header_navbar",
            'Modules\Search\Composers\HeaderNavbarComposer'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/search');

        $sourcePath = __DIR__.'/../Resources/views';

//        $this->publishes([
//            $sourcePath => $viewPath
//        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/search';
        }, \Config::get('view.paths')), [$sourcePath]), 'search');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/search');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'search');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'search');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
