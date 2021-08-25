<?php

namespace Modules\Projects\Http\Requests;

class ApproveMemberRequest extends HandleMemberRequest
{
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
}
