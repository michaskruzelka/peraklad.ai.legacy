<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Project;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Release;

class UpdateReleaseRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Release
     */
    protected $release;

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
        if ( ! $this->getRelease()
            || in_array($this->getRelease()->getState(), [
                array_search('failed', config('projects.states')),
                array_search('destroyed', config('projects.states'))
            ])
        ) {
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
        if ( ! $this->getRelease()->belongsToYou()) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id' => 'required',
            'rip_name' => 'max:100',
            'orthography' => 'size:1',
            'mode' => 'max:5'
        ];

        return $rules;
    }

    /**
     * @return Release
     */
    public function getRelease()
    {
        if ( ! $this->release) {
            $this->release = $this->dm->find(Release::class, $this->input('id'));
        }
        return $this->release;
    }

    /**
     * @return string
     */
    public function getOrthography()
    {
        return $this->input('orthography');
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->input('mode');
    }

    /**
     * @return string
     */
    public function getRipName()
    {
        return $this->input('rip_name');
    }

    /**
     * @return string
     */
    public function getEpisodeId()
    {
        return $this->input('episode_id');
    }

    /**
     * @return Project
     * @throws \Exception
     */
    public function getProject()
    {
        $project = $this->dm->getRepository(Project::class)->getByRelease($this->getRelease());
        if ( ! $project->getId()) {
            throw new \Exception('Unable to find the project by release');
        }
        return $project;
    }
}
