<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Subtitle;
use Modules\Users\Entities\User;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class AddCommentRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var array
     */
    protected $replyTo;

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
        if ($this->getSubtitle()->isEditable()
            || $this->getSubtitle()->getRelease()->isPublic()
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
            'text' => 'required|max:500'
        ];
        return $rules;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return strip_tags(nl2br($this->input('text')), '<br>');
    }

    /**
     * @return array
     */
    public function getReplyTo()
    {
        if ( ! $this->replyTo) {
            $userRep = $this->dm->getRepository(User::class);
            $replyTo = array_unique(explode(',', $this->input('replyTo')));
            $replyTo = array_filter($replyTo, function($userId) use($userRep) {
                return $userId != \Auth::id() && $userRep->checkByUsername($userId);
            });
            $this->replyTo = $replyTo;
        }
        return $this->replyTo;
    }

    /**
     * @return Subtitle
     */
    public function getSubtitle()
    {
        return $this->route('subtitle');
    }
}
