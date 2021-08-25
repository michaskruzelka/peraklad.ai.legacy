<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Project;

class OpensubtitlesSearchRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Project
     */
    protected $episode;

    /**
     * OpensubtitlesSearchRequest constructor.
     */
    public function __construct()
    {
        $this->dm = app()->make(LaravelDocumentManager::class)->getDocumentManager();
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
        if ( ! $this->getProject()->getId()) {
            $this->failedValidation($this->getValidatorInstance());
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'projectId' => 'required|max:100',
            'episodeId' => 'max:100'
        ];
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        if ( ! $this->project) {
            $this->project = $this->dm->find(Project::class, $this->input('projectId'));
        }
        return $this->project;
    }

    /**
     * @return bool|Project
     */
    public function getEpisode()
    {
        if ( ! $this->episode) {
            if ('series' === $this->getProject()->getType()
                && $episodeId = $this->input('episodeId')
            ) {
                foreach ($this->getProject()->getEpisodes() as $episode) {
                    if ($episode->getId() == $episodeId) {
                        $this->episode = $episode;
                    }
                }
            }
        }
        return $this->episode;
    }
}
