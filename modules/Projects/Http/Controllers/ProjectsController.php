<?php

namespace Modules\Projects\Http\Controllers;

use App\Http\Requests;
use Modules\Projects\Http\Requests\DestroyProjectRequest;
use Modules\Projects\Http\Requests\ImdbSearchRequest;
use Modules\Projects\Http\Requests\KeywordsSearchRequest;
use Modules\Projects\Http\Requests\StoreProjectRequest;
use Modules\Projects\Http\Requests\UpdateProjectRequest;
use Pingpong\Modules\Routing\Controller;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Language;
use Modules\Projects\Entities\Project;

class ProjectsController extends Controller
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * ProjectsController constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    public function other()
    {
        return view('search::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        $langRepository = $this->dm->getRepository(Language::class);
        $languages = $langRepository->sort($langRepository->findSubable());
        $topLangsNum = $langRepository->getTopNumber();
        return view('projects::workshop.create', compact(
            'project', 'languages', 'topLangsNum', 'projects'
        ));
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  StoreProjectRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProjectRequest $request)
    {
        $result = ['status' => 'ok', 'response' => null];

        $projectInstances = $this->dm->getClassMetadata(Project::class)->discriminatorMap;
        $projectType = $request->input('movie-type');
        if ( ! array_key_exists($projectType, $projectInstances)) {
            $message = 'Unsupported project type: ' . $projectType;
            return $this->failResponse($message);
        }
        $project = app()->build($projectInstances[$projectType]);

        $language = $this->dm->find(Language::class, $request->input('lang'));
        if (is_null($language)) {
            $message = 'Unsupported language: ' . $request->input('lang');
            return $this->failResponse($message);
        }

        $project->getInfo()
            ->importLanguage($language)
            ->setImdbId($request->input('imdb'))
            ->setYear($request->input('year'))
            ->setPlot($request->input('plot'))
            ->setImdbRating($request->input('score'))
            ->setOriginalTitle($request->input('original_title'))
            ->setTranslatedTitle($request->input('translated_title'))
            ->getPoster()
            ->setRowData($request->input('poster'))
            ->import()
        ;

        if ('series' == $project->getType()) {
            $episodes = $request->getEpisodes();
            $project->importEpisodes($episodes);
        }

        $this->dm->persist($project);
        $this->dm->flush();

        flash()->success('Праект паспяхова захаваны');
        $result['response'] = ['url' => $project->getUrl()];
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $langRepository = $this->dm->getRepository(Language::class);
        $languages = $langRepository->sort($langRepository->findSubable());
        $topLangsNum = $langRepository->getTopNumber();
        return view('projects::workshop.edit', compact('project', 'languages', 'topLangsNum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request)
    {
        $result = ['status' => 'ok', 'response' => null];
        $project = $request->getProject();
        $language = $request->getLanguage();

        if ('series' == $project->getType()) {
            $episodes = $request->getEpisodes();
            try {
                $project->importEpisodes($episodes);
            } catch (\Exception $e) {
                $result = [
                    'status' => 'fail',
                    'response' => $e->getMessage()
                ];
                return response()->json($result);
            }
        }

        if ($project->belongsToYou()) {
            $project->getInfo()
                ->importLanguage($language)
                ->setImdbId($request->input('imdb'))
                ->setYear($request->input('year'))
                ->setPlot($request->input('plot'))
                ->setImdbRating($request->input('score'))
                ->setOriginalTitle($request->input('original_title'))
                ->setTranslatedTitle($request->input('translated_title'))
                ->getPoster()
                ->setRowData($request->input('poster'))
                ->import()
            ;
        }

        $this->dm->persist($project);
        $this->dm->flush();
        $result['response'] = ['message' => 'Праект паспяхова захаваны'];
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  DestroyProjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyProjectRequest $request)
    {
        try {
            $this->dm->remove($request->getProject());
            $this->dm->flush();
        } catch (\Exception $e) {
            $result = [
                'status' => 'fail',
                'response' => $e->getMessage()
            ];
            return response()->json($result);
        }
        $result = [
            'status' => 'ok',
            'response' => route('workshop::projects::my')
        ];
        return response()->json($result);
    }

    /**
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshCreateForm(Project $project)
    {
        $langRepository = $this->dm->getRepository(Language::class);
        $languages = $langRepository->sort($langRepository->findSubable());
        $topLangsNum = $langRepository->getTopNumber();
        if ($project->belongsToYou() ||  ! $project->getId()) {
            $tplName = 'projects::workshop.panels.project-form';
        } else {
            $tplName = 'projects::workshop.panels.project-form-tenant';
        }
        $view = view($tplName, compact('languages', 'topLangsNum', 'project'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshUploadPoster()
    {
        $view = view('projects::workshop.panels.upload-poster');
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param Project $project
     * @param ImdbSearchRequest $imdbSearchRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchImdb(Project $project, ImdbSearchRequest $imdbSearchRequest)
    {
        $projectInfo = $project->getInfo()
            ->setImdbId($imdbSearchRequest->input('imdb-id'))
            ->setOriginalTitle($imdbSearchRequest->input('title'))
        ;
        $isFound = $projectInfo->import();
        if ($isFound) {
            $result = ['status' => 'ok', 'response' => [
                'title' => $projectInfo->getOriginalTitle(),
                'id' => $projectInfo->getImdbId(),
                'rating' => $projectInfo->getImdbRating(),
                'plot' => $projectInfo->getPlot(),
                'poster' => $projectInfo->getPoster()->getRowData(),
                'language' => $projectInfo->getLanguage()->getIso6393b(),
                'year' => $projectInfo->getYear(),
                'type' => $projectInfo->getGarbage()['type'],
                'episodes' => $projectInfo->getGarbage()['episodes']
            ]];
        } else {
            $result = ['status' => 'fail', 'response' => null];
        }
        return response()->json($result);
    }

    /**
     * @param KeywordsSearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function searchByKeywords(KeywordsSearchRequest $request)
    {
        $items = [];
        $projectsRepository = $this->dm->getRepository(Project::class);
        $key = $request->input('key');
        $page = $request->input('page') ? ($request->input('page') - 1) : 0;
        $totalCount = $projectsRepository->searchCount($key);
        if ($totalCount > 0) {
            $projects = $projectsRepository->search($key, $page);
            foreach ($projects as $project) {
                $items[] = [
                    'originalTitle' => $project->getInfo()->getOriginalTitle(),
                    'translatedTitle' => $project->getInfo()->getTranslatedTitle(),
                    'plot' => str_limit($project->getInfo()->getPlot(), 210),
                    'poster' => $project->getInfo()->getPoster()->getSrc(),
                    'year' => $project->getInfo()->getYear(),
                    'url' => $project->getUrl()
                ];
            }
        }
        $result = ['status' => 'ok', 'response' => [
            'items' => $items,
            'totalCount' => $totalCount
        ]];
        return response()->json($result);
    }

    protected function failResponse($message)
    {
        \Log::notice($message);
        $result = ['status' => 'fail', 'response' => $message];
        return response()->json($result, 422);
    }

    public function test()
    {
//        $tr = app()->make('Kurt\Google\Translate');
//        $l = $tr->getLanguages();

    }
}