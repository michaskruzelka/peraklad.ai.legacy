<?php

namespace Modules\Users\Http\Requests;

use App\Http\Requests\Request;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Users\Entities\User;

class RegisterRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var array
     */
    protected $prohibitedUsernames = [
        'all', 'me', 'hyi', 'hui', 'pizda', 'fuck', 'blya', 'suka'
    ];

    /**
     * RegisterRequest constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
        //$this->captcha = $captcha;
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
//        if ( ! app('captcha')->verify($this->getCaptcha(), $this->getClientIp())) {
//            $validator = $this->getValidatorInstance();
//            $validator->getMessageBag()->add('g-recaptcha-response', 'Робатам уваход забаронены');
//            $this->failedValidation($validator);
//        }
            if ($this->usernameExists()) {
            $validator = $this->getValidatorInstance();
            $validator->getMessageBag()->add('username', 'Такі username ужо існуе');
            $this->failedValidation($validator);
        } elseif ($this->emailExists()) {
            $validator = $this->getValidatorInstance();
            $validator->getMessageBag()->add('email', 'Такі email ужо існуе');
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
            'email' => 'required|max:30|email',
            'username' => ['required', 'max:20', 'min:3', 'regex:/^(([a-z\d]+)([\\_\-\.]*))*([a-z\d]+)$/i'],
            'name' => 'required|max:30',
            'password' => 'required|max:20|min:5',
            'duplicated_password' => 'same:password',
            'captcha' => 'required|captcha'
        ];

        return $rules;
    }

    /**
     * @return string
     */
    public function getCaptcha()
    {
        return $this->input('captcha');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return trim($this->input('email'));
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->input('password');
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return trim($this->input('username'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return trim(strip_tags($this->input('name')));
    }

    /**
     * @return string
     */
    public function getDuplicatedPassword()
    {
        return $this->input('duplicated_password');
    }

    /**
     * @return bool
     */
    public function usernameExists()
    {
        return in_array($this->getUsername(), $this->prohibitedUsernames)
            || $this->dm->getRepository(User::class)->checkByUsername($this->getUsername())
        ;
    }

    /**
     * @return bool
     */
    public function emailExists()
    {
        return $this->dm->getRepository(User::class)->checkByEmail($this->getEmail());
    }
}
