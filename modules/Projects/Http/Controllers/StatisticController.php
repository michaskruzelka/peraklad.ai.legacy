<?php

namespace Modules\Projects\Http\Controllers;

use App\Http\Requests;
use Modules\Projects\Entities\Release;
use Modules\Projects\Entities\ReleaseDownload;
use Modules\Projects\Entities\Subtitle;
use Pingpong\Modules\Routing\Controller;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class StatisticController extends Controller
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * ReleasesController constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * @param Release $release
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Release $release)
    {
        $period = 'day';
        $subtitlesRep = $this->dm->getRepository(Subtitle::class);
        $downloadsRep = $this->dm->getRepository(ReleaseDownload::class);
        $counts = $subtitlesRep->countByStatuses($release->getId(true));
        if ($release->isCompleted()) {
            $unfinishedSubsCount = 0;
        } else {
            $unfinishedSubsCount = ($counts['clean'] + $counts['underway']);
        }
        $downloads = $downloadsRep->countByAbc($release->getId(true));
        $progressTime = $release->getProgressTime();
        $progressDays = intval(intval($progressTime) / (3600*24));
        $periodTrends = $subtitlesRep->getTrends($release, $period);
        $graphs = $this->getGraphs($release);
        return view('projects::workshop.statistic', compact(
            'release', 'progressDays', 'unfinishedSubsCount', 'periodTrends',
            'graphs', 'period', 'counts', 'downloads'
        ));
    }

    /**
     * @param Release $release
     * @param $period
     * @return \Illuminate\Http\JsonResponse
     */
    public function trend(Release $release, $period)
    {
        $subtitlesRep = $this->dm->getRepository(Subtitle::class);
        $periodTrends = $subtitlesRep->getTrends($release, $period);
        $graphs = $this->getGraphs($release);
        $view = view('projects::workshop.statistic.trends', compact(
            'periodTrends', 'graphs', 'period'
        ));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param Release $release
     * @return array
     */
    protected function getGraphs(Release $release)
    {
        $graphs = [
            [
                'id' => 'sum',
                'valueField' => 'sum',
                'balloonText' => 'Усяго: [[value]]',
                'bullet' => 'round',
                'title' => 'Усяго',
                'fillAlphas' => 0.3,
                'fillColors' => '#62a8ea'
            ]
        ];
        foreach ($release->getConfirmedMembers() as $member) {
            $graphs[] = [
                'id' => $member->getUserId(),
                'valueField' => $member->getUserId(),
                'balloonText' => "{$member->getUserId()}: [[value]]",
                'bullet' => 'round',
                'title' => $member->getUserId(),
                'hidden' => true
            ];
        }
        return $graphs;
    }
}