<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;

class CompleteReleaseRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->getRelease()->belongsToYou()) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @return \Illuminate\Routing\Route|object|string
     */
    public function getRelease()
    {
        return $this->route('release');
    }
}
