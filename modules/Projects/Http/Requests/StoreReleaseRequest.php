<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Modules\Projects\Contracts\SubtitlesApi;
use Captioning\File as CaptionFile;
use Modules\Projects\Services\LanguageDetector;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Project;

class StoreReleaseRequest extends Request
{
    /**
     * @var
     */
    protected $fileContent;

    /**
     * @var SubtitlesApi
     */
    protected $subtitlesApi;

    /**
     * @var CaptionFile
     */
    protected $captionFile;

    /**
     * @var LanguageDetector
     */
    protected $detector;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Project
     */
    protected $episode;

    /**
     * StoreReleaseRequest constructor.
     */
    public function __construct()
    {
        $this->captionFile = app()->make(CaptionFile::class);
        $this->detector = app()->make(LanguageDetector::class);
        $this->dm = app()->make(LaravelDocumentManager::class)->getDocumentManager();
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
        $file = $this->getFile();
        if ($file instanceof UploadedFile
            && $file->getClientMimeType() != 'application/x-subrip'
        ) {
            $this->failedValidation($this->getValidatorInstance());
        }
        if ( ! $this->getProject()->getId()) {
            $this->failedValidation($this->getValidatorInstance());
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
            'new_rip_name' => 'required|max:100',
            'orthography' => 'required|size:1',
            'opensubtitles_id' => 'required_without:new_release_file',
            //'opensubtitles_charset' => 'required_with:opensubtitles_id',
            'new_release_file' => 'required_without:opensubtitles_id|max:10000000',
            'projectId' => 'required'
        ];

        return $rules;
    }

    /**
     * @return mixed|string
     */
    public function getSrtContent()
    {
        if ( ! $this->fileContent) {
            if ( ! $id = $this->get('opensubtitles_id')) {
                $file = $this->getFile()->openFile();
                $this->fileContent = trim($file->fread($file->getSize()));
            } else {
                $this->fileContent = trim($this->getSubtitlesApi()->download($id));
            }
        }
        return $this->fileContent;
    }

    /**
     * Opensubtitles.org returns incorrect charset.
     * So we need to detect that ourselves by means of 'uchardet' external ubuntu program
     * @return string
     */
    public function getSrtCharset()
    {
        if ( ! ($this->getFile() instanceof UploadedFile)) {
            try {
                $path = '/tmp/' . md5(microtime());
                file_put_contents($path, $this->getSrtContent());
            } catch (\Exception $e) {
                \Log::warning($e->getMessage());
                return $this->get('opensubtitles_charset');
            }
        } else {
            $path = $this->getFile()->getRealPath();
        }
        $exec = [];
        exec('uchardet "'. $path .'"', $exec);
        $charset = current(explode('/', $exec[0]));
        return $charset;
    }

    /**
     * @return array|null|UploadedFile
     */
    public function getFile()
    {
        return $this->file('new_release_file');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCaptions()
    {
        $charset = $this->getSrtCharset();
        $content = $this->getSrtContent();
        $captionFile = $this->captionFile;
        $captionFile->setEncoding($charset)
            ->loadFromString($content)
        ;

        if ($captionFile->getCuesCount() == 0) {
            throw new \Exception('Некарэктны файл субтытраў');
        }

        $sampleText = '';
        foreach ($captionFile->getCues() as $index => $cue) {
            $sampleText .= $cue->getText() . ' ';
            if ($index > 30) break;
        }

        $isBel = $this->detector->isBel($sampleText);
        if ($isBel &&  ! $this->get('isTranslated')) {
            throw new \Exception('Субтытры не могуць быць беларускімі');
        } elseif ( ! $isBel && $this->get('isTranslated')) {
            throw new \Exception('Субтытры павінны быць беларускімі');
        }

        return $captionFile->getCues();
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        if ( ! $this->project) {
            $this->project = $this->dm->find(Project::class, $this->getProjectId());
        }
        return $this->project;
    }

    /**
     * @return bool|Project
     */
    public function getEpisode()
    {
        if ( ! $this->episode) {
            if ('series' === $this->getProject()->getType()
                && $episodeId = $this->input('release_episode')
            ) {
                foreach ($this->getProject()->getEpisodes() as $episode) {
                    if ($episode->getId() == $episodeId) {
                        $this->episode = $episode;
                    }
                }
            }
        }
        return $this->episode;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        $project = $this->getProject();
        $movieOriginalName = $project->getInfo()->getOriginalTitle();
        if ($episode = $this->getEpisode()) {
            $movieOriginalName .= ' | ' . $episode->getInfo()->getOriginalTitle();
            $movieOriginalName .= ' (' . $episode->getInfo()->getYear() . ')';
        } else {
            $movieOriginalName .= ' (' . $project->getInfo()->getYear() . ')';
        }
        return $movieOriginalName;
    }

    /**
     * @return string
     */
    public function getTranslatedName()
    {
        $name = $this->getProject()->getInfo()->getTranslatedTitle();
        if ($episode = $this->getEpisode()) {
            $name .= ' | ' . $episode->getInfo()->getTranslatedTitle();
        }
        return $name;
    }

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->input('projectId');
    }

    /**
     * @return SubtitlesApi
     */
    public function getSubtitlesApi()
    {
        if ( ! $this->subtitlesApi) {
            $this->subtitlesApi = $this->subtitlesApi = app()->make(SubtitlesApi::class);
        }
        return $this->subtitlesApi;
    }
}
