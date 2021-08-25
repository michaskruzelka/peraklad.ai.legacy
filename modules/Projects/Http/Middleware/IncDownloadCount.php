<?php

namespace Modules\Projects\Http\Middleware;

use Closure;
use Modules\Projects\Entities\ReleaseDownload;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Release;

class IncDownloadCount
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $id = $request->route('release')->getId(true);
        $dm = app()->make(LaravelDocumentManager::class)->getDocumentManager();
        $dm->createQueryBuilder(Release::class)
            ->update()
            ->field('_id')->equals($id)
            ->field('loads')->inc(1)
            ->getQuery()
            ->execute()
        ;
        $download = app()->build(ReleaseDownload::class);
        $download->setAbc($request->route('format'))
            ->setRelease($request->route('release'))
        ;
        $dm->persist($download);
        $dm->flush();
    }
}