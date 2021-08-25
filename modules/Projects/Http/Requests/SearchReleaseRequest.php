<?php

namespace Modules\Projects\Http\Requests;

use Modules\Projects\Entities\Language;
use Modules\Users\Entities\User;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use App\Http\Requests\Request;
use Auth;

class SearchReleaseRequest extends Request
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * ProjectsController constructor.
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
//        if ($this->getUserId() &&  ! \MongoId::isValid($this->getUserId())) {
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
        $beforeYear = date('Y',strtotime('+1 year'));
        return [
            'search' => 'max:100',
            'lang' => 'size:3',
            'year' => "date_format:Y|before:" . $beforeYear . "|after:1899"
        ];
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return  ! $this->route('userId') || $this->route('userId') == 'all'
            ? Auth::id()
            : $this->route('userId')
        ;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return strip_tags($this->get('search'));
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return strip_tags($this->get('lang'));
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return strip_tags($this->get('year'));
    }

    /**
     * @return bool
     */
    public function getExcludeUsersFlag()
    {
        if ($this->getUserId() == Auth::id()
            &&  ! in_array('me', $this->segments())
            &&  ! in_array(Auth::id(), $this->segments())
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        if (is_null($this->route('mode'))) {
            return 'all';
        } elseif ($mode = array_search($this->route('mode'), config('projects.modes'))) {
            return $mode;
        }
        return $this->route('mode');
    }

    /**
     * @return string
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    public function generateTitle()
    {
        $title = 'Праекты';
        $userId = $this->route('userId');
        if (($userId && $userId != 'all') || in_array('me', $this->segments())) {
            if (in_array('me', $this->segments())) {
                $userId = Auth::id();
            }
            $title .= ' - ' . $userId;
        }
        $title .= $this->getMode() != 'all'
            ? ' - ' . ($this->getMode() == 'pr' ? 'Прыватны рэжым' : 'Публічны рэжым')
            : ''
        ;
        $title .= $this->getSearch() ? ' - Пошук' : '';
        $title .= $this->getYear() ? ' - ' . $this->getYear() : '';
        if ($this->getLang() != '') {
            $lang = $this->dm->getRepository(Language::class)->find($this->getLang());
            $title .= ' - ' . $lang->getBelName();
        }
        $state = $this->route('state');
        if ($state && $state != 'all') {
            $title .= ' - ' . config('projects.statesDetailed.' . $state . '.title');
        }
        return $title;
    }
}
