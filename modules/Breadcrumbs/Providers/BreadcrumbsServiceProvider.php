<?php namespace Modules\Breadcrumbs\Providers;

use Illuminate\Support\ServiceProvider;

class BreadcrumbsServiceProvider extends ServiceProvider {

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
//		$this->publishes([
//		    __DIR__.'/../Config/config.php' => config_path('breadcrumbs.php'),
//		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'breadcrumbs'
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
            "{$theme}::partials.page_main",
            'Modules\Breadcrumbs\Composers\PageMainComposer'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('resources/views/modules/breadcrumbs');

		$sourcePath = __DIR__.'/../Resources/views';

//		$this->publishes([
//			$sourcePath => $viewPath
//		]);

		$this->loadViewsFrom(array_merge(array_map(function ($path) {
			return $path . '/modules/breadcrumbs';
		}, \Config::get('view.paths')), [$sourcePath]), 'breadcrumbs');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/breadcrumbs');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'breadcrumbs');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'breadcrumbs');
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
