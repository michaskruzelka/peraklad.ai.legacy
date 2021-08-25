<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Release;

class DestroyReleaseRequest extends Request
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
        if ( ! $this->getRelease()) {
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
            'id' => 'required'
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
}
