<?php

namespace Modules\Projects\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Projects\Entities\Release;
use Modules\Projects\Entities\Subtitle;
use Modules\Projects\Entities\Project;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class DestroyRelease extends Job implements SelfHandling, ShouldQueue
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
        $release = $dm->find(Release::class, $this->releaseId);
        if ($release->getId() && $release->getState() == array_search('failed', config('projects.states'))) {
            $dm->getRepository(Subtitle::class)->removeByRelease($release);
            $dm->getRepository(Project::class)->pullRelease($release);
            $release->setDestroyedState()->resetFiles();
            $dm->persist($release);
            $dm->flush();
        }
    }
}
