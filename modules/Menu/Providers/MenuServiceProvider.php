<?php

namespace Modules\Menu\Providers;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
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
		$this->app->bind(
            'Modules\Menu\Contracts\CollectorContract',
            'Modules\Menu\Services\Collector\ModulesService'
        );
	}

	/**
	 * Register config.
	 * 
	 * @return void
	 */
	protected function registerConfig()
	{
//		$this->publishes([
//		    __DIR__.'/../Config/config.php' => config_path('menu.php'),
//		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'menu'
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
            "{$theme}::partials.left_navbar",
            'Modules\Menu\Composers\LeftNavbarComposer'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('resources/views/modules/menu');

		$sourcePath = __DIR__.'/../Resources/views';

//		$this->publishes([
//			$sourcePath => $viewPath
//		]);

		$this->loadViewsFrom(array_merge(array_map(function ($path) {
			return $path . '/modules/menu';
		}, \Config::get('view.paths')), [$sourcePath]), 'menu');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/menu');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'menu');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'menu');
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
