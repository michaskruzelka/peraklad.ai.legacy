<?php namespace Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider {

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
//		    __DIR__.'/../Config/config.php' => config_path('notifications.php'),
//		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'notifications'
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
			'Modules\Notifications\Composers\HeaderNavbarComposer'
		);
		view()->composer(
            "{$theme}::partials.left_navbar",
            'Modules\Notifications\Composers\LeftNavbarComposer'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('resources/views/modules/notifications');

		$sourcePath = __DIR__.'/../Resources/views';

//		$this->publishes([
//			$sourcePath => $viewPath
//		]);

		$this->loadViewsFrom(array_merge(array_map(function ($path) {
			return $path . '/modules/notifications';
		}, \Config::get('view.paths')), [$sourcePath]), 'notifications');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/notifications');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'notifications');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'notifications');
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
