<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Subtitle;

class ViewSubtitleRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Subtitle
     */
    protected $subtitle;

    /**
     * ViewSubtitleRequest constructor.
     * @param LaravelDocumentManager $documentManager
     */
    public function __construct(LaravelDocumentManager $documentManager)
    {
        $this->dm = $documentManager->getDocumentManager();
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
        if ( ! \MongoId::isValid($this->getReleaseId())) {
            abort(404);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'search' => 'max:100'
        ];

        return $rules;
    }

    /**
     * @return \Illuminate\Routing\Route|object|string
     */
    public function getReleaseId()
    {
        return $this->route('releaseId');
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return strip_tags($this->get('search'));
    }

    /**
     * @return Subtitle
     */
    public function getSubtitle()
    {
        if ( ! $this->subtitle) {
            $releaseId = $this->getReleaseId();
            $number = $this->route('n') ?  (int) $this->route('n') : false;
            $status = $this->route('status') ? $this->route('status') : 'all';
            $this->subtitle = $this->dm->getRepository(Subtitle::class)
                ->getByNumber($releaseId, $status, $number, $this->getSearch());
        }
        return $this->subtitle;
    }
}
