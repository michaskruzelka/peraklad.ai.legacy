<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Subtitle;
use Modules\Projects\Entities\SubtitleVersion;

class ApproveSubtitleVersionRequest extends Request
{
    /**
     * @var SubtitleVersion
     */
    protected $version;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->getSubtitle()->isEditable()
            && $this->getVersion()
            &&  ! $this->getVersion()->isOwner()
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
            'id' => 'required',
            'checked' => 'in:true,false'
        ];
        return $rules;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->input('checked') == 'true' ? true : false;
    }

    /**
     * @return SubtitleVersion|bool
     */
    public function getVersion()
    {
        if ( ! $this->version) {
            foreach ($this->getSubtitle()->getVersions() as $version) {
                if ($version->getId() == $this->input('id')) {
                    $this->version = $version;
                    return $this->version;
                }
            }
        }
        return $this->version;
    }

    /**
     * @return Subtitle
     */
    public function getSubtitle()
    {
        return $this->route('subtitle');
    }
}
