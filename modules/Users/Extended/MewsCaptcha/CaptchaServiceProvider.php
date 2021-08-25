<?php

namespace Modules\Users\Extended\MewsCaptcha;

use Mews\Captcha\CaptchaServiceProvider as ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Merge configs
        $this->mergeConfigFrom(
            base_path('vendor/mews/captcha/config/captcha.php'), 'captcha'
        );

        // Bind captcha
        $this->app->bind('captcha', function($app)
        {
            return new Captcha(
                $app['Illuminate\Filesystem\Filesystem'],
                $app['Illuminate\Config\Repository'],
                $app['Intervention\Image\ImageManager'],
                $app['Illuminate\Session\Store'],
                $app['Illuminate\Hashing\BcryptHasher'],
                $app['Illuminate\Support\Str']
            );
        });
    }
}