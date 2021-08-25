<?php

namespace Modules\Users\Http\Requests;

use App\Http\Requests\Request;

class LoginRequest extends Request
{
    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
//        if ( ! $this->getRelease()->getId()) {
//            $this->failedValidation($this->getValidatorInstance());
//        }
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
            'email' => 'required|max:30',
            'password' => 'required|max:20|min:5'
        ];

        return $rules;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->input('password');
    }

    /**
     * @return bool
     */
    public function hasRemember()
    {
        return $this->has('remember');
    }

    /**
     * @return bool|string
     */
    public function getEmail()
    {
        $email = $this->input('email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            return $email;
        }
        return false;
    }

    /**
     * @return bool|string
     */
    public function getUsername()
    {
        $username = $this->input('email');
        if (filter_var($username, FILTER_VALIDATE_EMAIL) !== false) {
            return false;
        }
        return $username;
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        $keys = [
            'password' => $this->getPassword()
        ];
        if ($email = $this->getEmail()) {
            $keys['email'] = $email;
        }
        if ($username = $this->getUsername()) {
            $keys['_id'] = $username;
        }
        return $keys;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        if ( ! $url = $this->session()->get('auth_ref')) {
            return route(config('users.redirects.login'));
        }
        return $url;
    }
}
