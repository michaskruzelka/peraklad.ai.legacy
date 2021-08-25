<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Subtitle;
use Modules\Projects\Entities\SubtitleComment;

class RemoveSubtitleCommentRequest extends Request
{
    /**
     * @var SubtitleComment
     */
    protected $comment;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (($this->getSubtitle()->isEditable()
            || $this->getSubtitle()->getRelease()->isPublic())
            && $this->getComment()
            && $this->getComment()->isOwner()
        ) {
            return true;
        }
        return false;
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
     * @return SubtitleComment|bool
     */
    public function getComment()
    {
        if ( ! $this->comment) {
            foreach ($this->getSubtitle()->getComments() as $comment) {
                if ($comment->getId() == $this->input('id')) {
                    $this->comment = $comment;
                    return $this->comment;
                }
            }
        }
        return $this->comment;
    }

    /**
     * @return Subtitle
     */
    public function getSubtitle()
    {
        return $this->route('subtitle');
    }
}
