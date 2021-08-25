<?php

namespace Modules\Users\Http\Requests;

use App\Http\Requests\Request;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Users\Entities\User;

class RecoverRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var User
     */
    protected $user;

    /**
     * RegisterRequest constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
        if ( ! $this->getUser()) {
            $validator = $this->getValidatorInstance();
            $validator->getMessageBag()->add('email', 'Такі email не існуе');
            $this->failedValidation($validator);
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
        $rules = [
            'email' => 'required|max:30|email'
        ];

        return $rules;
    }

    /**
     * @return bool|string
     */
    public function getEmail()
    {
        return $this->input('email');
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if ( ! $this->user) {
            $this->user = $this->dm->getRepository(User::class)
                ->findOneByEmail($this->getEmail())
            ;
        }
        return $this->user;
    }
}
