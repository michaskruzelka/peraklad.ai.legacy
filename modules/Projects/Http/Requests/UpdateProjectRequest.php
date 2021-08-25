<?php

namespace Modules\Projects\Http\Requests;

use Modules\Projects\Entities\Project;
use Modules\Projects\Entities\Language;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class UpdateProjectRequest extends StoreProjectRequest
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
     * @var Language
     */
    protected $language;

    /**
     * ProjectsController constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ('movie' == $this->getProject()->getType()
            &&  ! $this->getProject()->belongsToYou()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
        if ( ! $this->getProject()) {
            $validator = $this->getValidatorInstance();
            $validator->getMessageBag()->add('id', 'Unable to find the requested project');
            $this->failedValidation($validator);
        }
        if ($this->getProject()->belongsToYou() &&  ! $this->getLanguage()) {
            $validator = $this->getValidatorInstance();
            $validator->getMessageBag()->add('lang', 'Unsupported language');
            $this->failedValidation($validator);
        }
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
        $rules = array_merge(parent::rules(), $rules);
        unset($rules['movie-type']);
        if ( ! $this->getProject()->belongsToYou()) {
            $rules = array_diff_key($rules, array_flip(array_keys($this->getMainRules())));
        }
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

    /**
     * @return Language
     */
    public function getLanguage()
    {
        if ( ! $this->language) {
            $this->language = $this->dm->find(Language::class, $this->input('lang'));
        }
        return $this->language;
    }
}
