<?php

namespace Modules\Users\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Users\Entities\UserAvatar;

class DeleteAvatar extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var UserAvatar
     */
    protected $avatar;

    /**
     * Create a new job instance.
     * @param UserAvatar $avatar
     */
    public function __construct(UserAvatar $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = 'public/modules/users/avatars/' . $this->avatar->getFileName();
        if ( ! $this->avatar->isFileDefault() && \Storage::disk()->has($file)) {
            \Storage::delete($file);
        }
    }
}
