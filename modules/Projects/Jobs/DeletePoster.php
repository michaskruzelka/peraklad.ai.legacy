<?php

namespace Modules\Projects\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Projects\Entities\ProjectInfoPoster;

class DeletePoster extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var ProjectInfoPoster
     */
    protected $poster;

    /**
     * Create a new job instance.
     * @param ProjectInfoPoster $poster
     */
    public function __construct(ProjectInfoPoster $poster)
    {
        $this->poster = $poster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = 'public/modules/projects/img/posters/' . $this->poster->getFileName();
        if ( ! $this->poster->isFileDefault() && \Storage::disk()->has($file)) {
            \Storage::delete($file);
        }
    }
}
