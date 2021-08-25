<?php

namespace Modules\Users\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Modules\Users\Entities\User;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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
     * @param Router $router
     * @param LaravelDocumentManager $ldm
     */
    public function boot(Router $router, LaravelDocumentManager $ldm)
    {
        $this->bootUser($router, $ldm);
    }

    protected function bootUser($router, $ldm)
    {
        $router->bind('user', function ($value) use ($ldm) {
            if (is_null($value)) {
                return;
            }
            if ('me' == $value) {
                return \Auth::user();
            }
            $dm = $ldm->getDocumentManager();
            $filter = [
                'id' => $value,
                'st' => User::ACTIVE_STATUS
            ];
            if ($user = $dm->getRepository(User::class)->findOneBy($filter)) {
                return $user;
            }
            throw new NotFoundHttpException;
        });
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
