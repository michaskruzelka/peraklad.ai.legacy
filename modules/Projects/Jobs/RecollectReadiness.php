<?php

namespace Modules\Projects\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Projects\Entities\Release;
use Modules\Projects\Entities\Subtitle;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class RecollectReadiness extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    protected $releaseId;

    /**
     * Create a new job instance.
     * @param string $releaseId
     */
    public function __construct($releaseId)
    {
        $this->releaseId = $releaseId;
    }

    /**
     * Execute the job.
     *
     * @param LaravelDocumentManager $ldm
     * @return void
     */
    public function handle(LaravelDocumentManager $ldm)
    {
        $dm = $ldm->getDocumentManager();
        $readiness = $dm->getRepository(Subtitle::class)->getReadiness($this->releaseId);
        $readiness = round($readiness, 1) . '%';
        $dm->createQueryBuilder(Release::class)
            ->update()
            ->field('re')->set($readiness)
            ->field('_id')->equals(new \MongoId($this->releaseId))
            ->getQuery()
            ->execute()
        ;
    }
}
