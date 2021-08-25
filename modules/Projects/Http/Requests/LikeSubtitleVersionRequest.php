<?php

namespace Modules\Projects\Http\Requests;

class LikeSubtitleVersionRequest extends ApproveSubtitleVersionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (($this->getSubtitle()->isEditable() || $this->getSubtitle()->getRelease()->isPublic())
            && $this->getVersion()
            &&  ! $this->getVersion()->isOwner()
        ) {
            return true;
        }
        return false;
    }
}
