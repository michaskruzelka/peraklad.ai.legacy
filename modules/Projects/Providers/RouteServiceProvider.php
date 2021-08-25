<?php namespace Modules\Projects\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Intervention\Image\Exception\NotFoundException;
use Modules\Projects\Entities\Project;
use Modules\Projects\Entities\Release;
use Modules\Projects\Entities\Subtitle;
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
		$this->bootProject($router, $ldm);
		$this->bootRelease($router, $ldm);
		$this->bootSubtitle($router, $ldm);
	}

    protected function bootSubtitle($router, $ldm)
    {
        $router->bind('subtitle', function ($value) use ($ldm) {
            if (is_null($value)) {
                return;
            }
            $dm = $ldm->getDocumentManager();
            if ($subtitle = $dm->getRepository(Subtitle::class)->find($value)) {
                return $subtitle;
            }
            throw new NotFoundException;
        });
    }

    protected function bootRelease($router, $ldm)
    {
        $router->bind('release', function ($value) use($ldm) {
            if (is_null($value)) {
                return;
            }
            $dm = $ldm->getDocumentManager();
            if ($release = $dm->getRepository(Release::class)->find($value)) {
                return $release;
            }
            throw new NotFoundHttpException;
        });
    }

    protected function bootProject($router, $ldm)
    {
        $router->bind('project', function ($value) use($ldm) {
            if (is_null($value)) {
                return;
            }
            $dm = $ldm->getDocumentManager();
            if ($project = $dm->getRepository(Project::class)->findOneBySlug($value)) {
                return $project;
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
