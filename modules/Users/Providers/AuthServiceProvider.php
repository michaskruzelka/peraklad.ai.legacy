<?php

namespace Modules\Users\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('mongo', function($app) {
            $ldm = $app->make(LaravelDocumentManager::class);
            return new MongoUserProvider($ldm);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}