<?php namespace Modules\Users\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Users\Contracts\GenderDetector;
use Modules\Users\Services\GenderDetector\GenderApiCom;
use Modules\Users\Contracts\Geocoder;
use Modules\Users\Services\Geocoder\Google;

class UsersServiceProvider extends ServiceProvider {

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
		$this->app->singleton(GenderDetector::class, GenderApiCom::class);
		$this->app->singleton(Geocoder::class, Google::class);
	}

    public function addValidators()
    {
        //Validator::extend('usernameExists', UsernameExistsValidator::class . '@validate');
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
            'Modules\Users\Composers\HeaderNavbarComposer'
        );
        view()->composer(
            "{$theme}::partials.left_navbar",
            'Modules\Users\Composers\LeftNavbarComposer'
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
//		    __DIR__.'/../Config/config.php' => config_path('users.php'),
//		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'users'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('resources/views/modules/users');

		$sourcePath = __DIR__.'/../Resources/views';

//		$this->publishes([
//			$sourcePath => $viewPath
//		]);

		$this->loadViewsFrom(array_merge(array_map(function ($path) {
			return $path . '/modules/users';
		}, \Config::get('view.paths')), [$sourcePath]), 'users');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/users');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'users');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'users');
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
