<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Release;
use Modules\Users\Entities\User;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Auth;

class HandleMemberRequest extends Request
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
     * HandleMemberRequest constructor.
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
        if ($this->getMemberId() != Auth::id()
            &&  ! $this->getRelease()->belongsToYou()
        ) {
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
        return $this->route('release');
    }

    /**
     * @return string
     */
    public function getMemberId()
    {
        return $this->input('id');
    }

    /**
     * @return User
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    public function getUser()
    {
        return $this->dm->getRepository(User::class)->find($this->getMemberId());
    }
}
