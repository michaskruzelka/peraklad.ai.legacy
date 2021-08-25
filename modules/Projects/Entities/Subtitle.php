<?php

namespace Modules\Projects\Entities;

use Captioning\Cue;
use Captioning\File as CaptionFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Projects\Jobs\RecollectReadiness;

/**
 * Class Subtitle
 * @package Modules\Projects\Entities
 * @ODM\Document(
 *     collection="subtitles",
 *     repositoryClass="Modules\Projects\Repositories\Subtitles",
 *     indexes={
 *          @ODM\Index(keys={"release"="asc", "n"="asc"}, options={"unique"=true})
 *     }
 * )
 */
class Subtitle
{
    use DispatchesJobs;

    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Release", simple=true)
     */
    private $release;

    /**
     * @ODM\Field(type="string", name="ot")
     */
    private $originalText;

    /**
     * @ODM\Field(type="string", name="tt")
     */
    private $translatedText;

    /**
     * @ODM\EmbedMany(targetDocument="SubtitleVersion")
     */
    private $vers;

    /**
     * @ODM\EmbedMany(targetDocument="SubtitleComment")
     */
    private $comms;

    /**
     * @ODM\Field(type="string", name="st")
     */
    private $status;

    /**
     * @ODM\Field(type="int", name="n")
     */
    private $number;

    /**
     * @ODM\EmbedOne(targetDocument="SubtitleTimeRange")
     */
    private $tr;

    /**
     * @ODM\EmbedMany(
     *     discriminatorField="type",
     *     discriminatorMap={
     *          "upload" = "SubtitleHistoryUpload",
     *          "translate" = "SubtitleHistoryChangeTranslation",
     *          "save" = "SubtitleHistorySaveTranslation",
     *          "timing" = "SubtitleHistoryChangeTiming",
     *          "addCom" = "SubtitleHistoryAddComment",
     *          "addVer" = "SubtitleHistoryAddVersion",
     *          "appVer" = "SubtitleHistoryApproveVersion",
     *          "delCom" = "SubtitleHistoryRemoveComment",
     *          "delVer" = "SubtitleHistoryRemoveVersion",
     *          "unAppVer" = "SubtitleHistoryUnapproveVersion"
     *     }
     * )
     */
    private $hist;

    public function __construct()
    {
        $this->vers = new ArrayCollection();
        $this->comms = new ArrayCollection();
        $this->hist = new ArrayCollection();
        $this->tr = app()->build(SubtitleTimeRange::class);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHistory()
    {
        return $this->hist;
    }

    /**
     * @param mixed $hist
     * @return $this
     */
    public function setHistory($hist)
    {
        $this->hist = $hist;
        return $this;
    }

    public function addHistoryEvent($event)
    {
        $this->hist->add($event);
    }

    /**
     * @return mixed
     */
    public function getRelease()
    {
        return $this->release;
    }

    /**
     * @param mixed $release
     * @return $this
     */
    public function setRelease($release)
    {
        $this->release = $release;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginalText()
    {
        return $this->originalText;
    }

    /**
     * @param mixed $originalText
     * @return $this
     */
    public function setOriginalText($originalText)
    {
        $this->originalText = $originalText;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslatedText()
    {
        return $this->translatedText;
    }

    /**
     * @param $translatedText
     * @param bool|true $addEvent
     * @return $this
     */
    public function setTranslatedText($translatedText, $addEvent = true)
    {
        if ($translatedText != $this->getTranslatedText() && $addEvent) {
            $event = app()->build(SubtitleHistoryChangeTranslation::class);
            $event->getInfo()->setTranslation($translatedText);
            $this->addHistoryEvent($event);
        }
        $this->translatedText = $translatedText;
        return $this;
    }

    /**
     * @param array $textLines
     * @return string
     */
    public function textLinesToText(array $textLines)
    {
        return implode('<br />', $textLines);
    }

    /**
     * @param string $text
     * @return string
     */
    public function formatText($text)
    {
        $text = $this->stripTags($text);
        $text = preg_replace('/(<br\s*\/?>\s*)+/iu', '<br />', $text);
        $text = preg_replace('/^(<br\s*\/?>\s*)?/iu', '', $text);
        $text = preg_replace('/(<br\s*\/?>\s*)?$/iu', '', $text);
        $text = strtr($text, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));
        $text = trim($text, chr(0xC2).chr(0xA0));
        return $text;
    }

    /**
     * @param $text
     * @param bool|false $all
     * @return string
     */
    public function stripTags($text, $all = false)
    {
        if ($all) {
            return strip_tags(trim($text));
        }
        return strip_tags(trim($text), '<br><b><i><u><font>');
    }

    /**
     * @param string $text
     * @return string
     */
    public function strictFormatText($text)
    {
        $text = str_replace(['<br />','<br/>','<br>'], ' ', $text);
        return strip_tags($text);
    }

    /**
     * @param $text
     * @return mixed
     */
    public function formatLineEnds($text)
    {
        $breaks = ['<br />', '<br>', '<br/>'];
        return str_ireplace($breaks, CaptionFile::WINDOWS_LINE_ENDING, $text);
    }

    /**
     * @return mixed
     */
    public function getVersions()
    {
        return $this->vers;
    }

    /**
     * @param mixed $versions
     * @return $this
     */
    public function setVersions($versions)
    {
        $this->vers = $versions;
        return $this;
    }

    /**
     * @param SubtitleVersion $version
     * @return $this
     */
    public function addVersion(SubtitleVersion $version)
    {
        $this->vers->add($version);
        $event = app()->build(SubtitleHistoryAddVersion::class);
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @param SubtitleVersion $version
     * @return $this
     */
    public function removeVersion(SubtitleVersion $version)
    {
        $version->setRemovedStatus();
        $event = app()->build(SubtitleHistoryRemoveVersion::class);
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @param SubtitleVersion $version
     * @return $this
     */
    public function approveVersion(SubtitleVersion $version)
    {
        $version->setApprovedStatus();
        $this->setTranslatedText($version->getText())
            ->setUnderwayStatus()
        ;
        $event = app()->build(SubtitleHistoryApproveVersion::class);
        $event->getInfo()->setTranslation(strip_tags($version->getText()))
            ->setRawData(['versionId' => new \MongoId($version->getId())])
        ;
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @param SubtitleVersion $version
     * @return $this
     */
    public function unapproveVersion(SubtitleVersion $version)
    {
        $version->setPendingStatus();
        $event = app()->build(SubtitleHistoryUnapproveVersion::class);
        $event->getInfo()->setTranslation(strip_tags($version->getText()))
            ->setRawData(['versionId' => new \MongoId($version->getId())])
        ;
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @param SubtitleVersion $version
     * @return $this
     */
    public function likeVersion(SubtitleVersion $version)
    {
        $version->like();
        $event = app()->build(SubtitleHistoryLikeVersion::class);
        $event->getInfo()->setTranslation(strip_tags($version->getText()))
            ->setRawData(['versionId' => new \MongoId($version->getId())])
        ;
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @param SubtitleVersion $version
     * @return $this
     */
    public function unlikeVersion(SubtitleVersion $version)
    {
        $version->unlike();
        $event = app()->build(SubtitleHistoryUnlikeVersion::class);
        $event->getInfo()->setTranslation(strip_tags($version->getText()))
            ->setRawData(['versionId' => new \MongoId($version->getId())])
        ;
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comms;
    }

    /**
     * @param mixed $comments
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comms = $comments;
        return $this;
    }

    /**
     * @param SubtitleComment $comment
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function addComment(SubtitleComment $comment)
    {
        $this->comms->add($comment);
        $event = app()->build(SubtitleHistoryAddComment::class);
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @param SubtitleComment $comment
     * @return $this
     */
    public function removeComment($comment)
    {
        //$this->comms->removeElement($comment);
        $comment->setRemovedStatus();
        $event = app()->build(SubtitleHistoryRemoveComment::class);
        $this->addHistoryEvent($event);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return $this
     */
    public function setUnderwayStatus()
    {
        $status = array_search('underway', config('projects.subtitleStatuses'));
        return $this->setStatus($status);
    }

    /**
     * @return $this
     */
    public function setCleanStatus()
    {
        $status = array_search('clean', config('projects.subtitleStatuses'));
        return $this->setStatus($status);
    }

    /**
     * @param bool|true $recollect
     * @param bool|true $addEvent
     * @return Subtitle
     */
    public function setSavedStatus($recollect = true, $addEvent = true)
    {
        $status = array_search('saved', config('projects.subtitleStatuses'));
        if ($status != $this->getStatus()) {
            if ($addEvent) {
                $event = app()->build(SubtitleHistorySaveTranslation::class);
                $this->addHistoryEvent($event);
            }
            if ($recollect) {
                $job = (new RecollectReadiness($this->getRelease()->getId()))->delay(5);
                $this->dispatch($job);
            }
        }
        return $this->setStatus($status);
    }

    /**
     * @return bool
     */
    public function isSaved()
    {
        return $this->getStatus() == array_search('saved', config('projects.subtitleStatuses'));
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeRange()
    {
        return $this->tr;
    }

    /**
     * @param mixed $tr
     * @return $this
     */
    public function setTimeRange($tr)
    {
        $this->tr = $tr;
        return $this;
    }

    /**
     * @param Cue $caption
     * @param Release $release
     * @param int $number
     * @return $this
     */
    public function importOriginal(Cue $caption, Release $release, $number)
    {
        $userId = \Auth::id();
        $uploadEvent = app()->build(SubtitleHistoryUpload::class);
        $uploadEvent->setUserId($userId);
        $uploadEvent->generateCreatedAt();
        $this->addHistoryEvent($uploadEvent);
        $originalText = $this->textLinesToText($caption->getTextLines());
        $originalText = $this->stripTags($originalText);
        $this->import($caption, $release, $number)
            ->setOriginalText($originalText)
            ->setCleanStatus()
        ;
        return $this;
    }

    /**
     * @param Cue $caption
     * @param Release $release
     * @param int $number
     * @return $this
     */
    public function importTranslated(Cue $caption, Release $release, $number)
    {
        $translatedText = $this->textLinesToText($caption->getTextLines());
        $translatedText = $this->stripTags($translatedText);
        $uploadEvent = app()->build(SubtitleHistoryUpload::class);
        $uploadEvent->generateUserId();
        $uploadEvent->generateCreatedAt();
        $this->addHistoryEvent($uploadEvent);
        $changeTrEvent = app()->build(SubtitleHistoryChangeTranslation::class);
        $changeTrEvent->generateUserId();
        $changeTrEvent->getInfo()->setTranslation($translatedText);
        $changeTrEvent->generateCreatedAt();
        $this->addHistoryEvent($changeTrEvent);
        $saveTrEvent = app()->build(SubtitleHistorySaveTranslation::class);
        $saveTrEvent->generateUserId();
        $saveTrEvent->generateCreatedAt();
        $this->addHistoryEvent($saveTrEvent);
        $this->import($caption, $release, $number)
            ->setTranslatedText($translatedText, false)
            ->setSavedStatus(false, false)
        ;
        return $this;
    }

    /**
     * @param Cue $caption
     * @param Release $release
     * @param int $number
     * @return $this
     */
    public function import(Cue $caption, Release $release, $number)
    {
        $this->setNumber($number)
            ->setRelease($release)
            ->getTimeRange()
            ->setBottomLine($caption->getStart())
            ->setTopLine($caption->getStop())
        ;
        return $this;
    }

    /**
     * @param CaptionFile $file
     * @return $this
     * @throws \Exception
     */
    public function export(CaptionFile $file)
    {
        $text = $this->formatLineEnds($this->getTranslatedText());
        if ($text && $this->isSaved()) {
            $file->addCue(
                $text,
                $this->getTimeRange()->getBottomLine(),
                $this->getTimeRange()->getTopLine()
            );
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return $this->getRelease()->belongsToYou()
            || $this->getRelease()->includesYou()
        ;
    }

    /**
     * @return bool
     */
    public function isViewable()
    {
        $release = $this->getRelease();
        return $release->isPublic()
            || $release->belongsToYou()
            || $release->includesYou()
        ;
    }

    /**
     * @return string|bool
     */
    public function getColor()
    {
        $colors = [
            'un' => 'grey-600',
            'cl' => 'grey-600',
            'sa' => 'blue-600'
        ];
        if (isset($colors[$this->getStatus()])) {
            return $colors[$this->getStatus()];
        }
        return false;
    }
}