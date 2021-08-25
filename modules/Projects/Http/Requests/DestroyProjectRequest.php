<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Project;

class DestroyProjectRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Project
     */
    protected $project;

    public function __construct()
    {
        $this->dm = app()->make(LaravelDocumentManager::class)->getDocumentManager();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( ! $this->getProject() ||  ! $this->getProject()->belongsToYou()) {
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
            'id' => 'required'
        ];

        return $rules;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        if ( ! $this->project) {
            $this->project = $this->dm->find(Project::class, $this->input('id'));
        }
        return $this->project;
    }
}
