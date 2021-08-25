<?php

namespace Modules\Users\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Users\Contracts\Geocoder;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Users\Entities\User;

class GenerateCoordinates extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $address;

    /**
     * Create a new job instance.
     * @param string $userId
     * @param string $address
     */
    public function __construct($userId, $address)
    {
        $this->userId = $userId;
        $this->address = $address;
    }

    /**
     * Execute the job.
     * @param Geocoder $geoCoder
     * @param LaravelDocumentManager $ldm
     * @return void
     */
    public function handle(Geocoder $geoCoder, LaravelDocumentManager $ldm)
    {
        $location = $geoCoder->getCoordinates($this->address);
        $latitude =  ! is_null($location) ? $location->latitude() : null;
        $longitude = ! is_null($location) ? $location->longitude() : null;
        $qb = $ldm->getDocumentManager()->createQueryBuilder(User::class);
        $qb->update()->field('address.loc.lng')->set($longitude)
            ->field('address.loc.lat')->set($latitude)
            ->field('_id')->equals($this->userId)
            ->getQuery()
            ->execute()
        ;
    }
}
