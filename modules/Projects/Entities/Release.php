<?php

namespace Modules\Projects\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth;
use Modules\Users\Entities\User;

/**
 * @ODM\Document(collection="releases", repositoryClass="Modules\Projects\Repositories\Releases")
 * @ODM\HasLifecycleCallbacks
 */
class Release
{
    const MODE_PRIVATE = 'pr';
    const MODE_PUBLIC = 'pu';

    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\EmbedOne(targetDocument="ReleaseOwner")
     */
    private $owner;

    /**
     * @ODM\Field(type="string")
     */
    private $state;

    /**
     * @ODM\Field(type="string", name="mon")
     */
    private $movieOriginalName;

    /**
     * @ODM\Field(type="string", name="mtn")
     */
    private $movieTranslatedName;

    /**
     * @var string
     * @ODM\Field(type="string", name="slug")
     */
    private $projectSlug;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    private $mode;

    /**
     * @var string
     * @ODM\Field(type="string", name="ort")
     */
    private $orthography;

    /**
     * @ODM\Field(type="string", name="rip")
     */
    private $ripName;

    /**
     * @ODM\Field(type="boolean", name="isGen")
     */
    private $isGenerated;

    /**
     * @ODM\EmbedMany(targetDocument="ReleaseFile")
     */
    private $files;

    /**
     * @ODM\Field(type="date", name="ca")
     */
    private $createdAt;

    /**
     * @ODM\EmbedMany(targetDocument="ReleaseMember")
     */
    private $members;

    /**
     * @var string
     * @ODM\Field(type="string", name="re")
     */
    private $readiness;

    /**
     * @var \DateTime
     * @ODM\Field(type="date", name="ua")
     */
    protected $updatedAt;

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    protected $loads;

    /**
     * @ODM\EmbedMany(
     *     discriminatorField="type",
     *     discriminatorMap={
     *          "complete" = "ReleaseHistoryComplete",
     *          "underway" = "ReleaseHistoryUnderway",
     *          "fail" = "ReleaseHistoryFail",
     *          "destroy" = "ReleaseHistoryDestroy"
     *     }
     * )
     */
    private $hist;

    public function __construct()
    {
        $this->owner = app()->build('Modules\Projects\Entities\ReleaseOwner');
        $this->files = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->hist = new ArrayCollection();
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

    /**
     * @param $event
     * @return $this
     */
    public function addHistoryEvent($event)
    {
        $this->hist->add($event);
        return $this;
    }

    /**
     * @return number
     */
    public function getProgressTime()
    {
        $timeDiffArray = [];
        foreach ($this->getHistory() as $event) {
            if ('underway' == $event->getType()) {
                $startPointTime = $event->getCreatedAt();
            } elseif (in_array($event->getType(), ['complete','fail','destroy'])
                && isset($startPointTime)
            ) {
                $endPointTime = $event->getCreatedAt();
                $timeDiff = $endPointTime->getTimestamp() - $startPointTime->getTimestamp();
                array_push($timeDiffArray, $timeDiff);
                unset($startPointTime);
            }
        }
        if (isset($startPointTime)) {
            $endPointTime = new \DateTime();
            $timeDiff = $endPointTime->getTimestamp() - $startPointTime->getTimestamp();
            array_push($timeDiffArray, $timeDiff);
        }
        return array_sum($timeDiffArray);
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        $event = current(array_filter($this->getHistory()->toArray(), function($event) {
            return 'underway' == $event->getType();
        }));
        if ( ! empty($event)) {
            return $event->getCreatedAt();
        }
        return new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        $event = last(array_filter($this->getHistory()->toArray(), function($event) {
            return in_array($event->getType(), ['complete','fail','destroy']);
        }));
        if ( ! empty($event)) {
            return $event->getCreatedAt();
        }
        return new \DateTime();
    }

    /**
     * @return int
     */
    public function getLoads()
    {
        return (int) $this->loads;
    }

    /**
     * @param $loads
     * @return $this
     */
    public function setLoads($loads)
    {
        $this->loads = $loads;
        return $this;
    }

    /**
     * @param bool $mongoId
     * @return mixed
     */
    public function getId($mongoId = false)
    {
        if ($mongoId) {
            return new \MongoId($this->id);
        }
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
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param bool|true $addEvent
     * @return Release
     */
    public function setUnderwayState($addEvent = true)
    {
        $state = array_search('underway', config('projects.states'));

        if ($state != $this->getState()) {
            if ($addEvent) {
                $event = app()->build(ReleaseHistoryUnderway::class);
                $this->addHistoryEvent($event);
            }
        }

        return $this->setState($state);
    }

    /**
     * @param bool|true $addEvent
     * @return Release
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setFailedState($addEvent = true)
    {
        $state = array_search('failed', config('projects.states'));

        if ($state != $this->getState()) {
            if ($addEvent) {
                $event = app()->build(ReleaseHistoryFail::class);
                $this->addHistoryEvent($event);
            }
        }

        return $this->setState($state);
    }

    /**
     * @param bool|true $addEvent
     * @return Release
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setDestroyedState($addEvent = true)
    {
        $state = array_search('destroyed', config('projects.states'));

        if ($state != $this->getState()) {
            if ($addEvent) {
                $event = app()->build(ReleaseHistoryDestroy::class);
                $this->addHistoryEvent($event);
            }
        }

        return $this->setState($state);
    }

    /**
     * @param bool|true $addEvent
     * @return Release
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setCompletedState($addEvent = true)
    {
        $state = array_search('completed', config('projects.states'));

        if ($state != $this->getState()) {
            if ($addEvent) {
                $event = app()->build(ReleaseHistoryComplete::class);
                $this->addHistoryEvent($event);
            }
        }

        return $this->setState($state);
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return array_search('completed', config('projects.states')) == $this->getState();
    }

    /**
     * @return bool
     */
    public function isUnderway()
    {
        return array_search('underway', config('projects.states')) == $this->getState();
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return array_search('underway', config('projects.states')) == $this->getState();
    }

    /**
     * @return bool
     */
    public function isDestroyed()
    {
        return array_search('destroyed', config('projects.states')) == $this->getState();
    }

    /**
     * @return mixed
     */
    public function getMovieOriginalName()
    {
        return $this->movieOriginalName;
    }

    /**
     * @param mixed $movieOriginalName
     * @return $this
     */
    public function setMovieOriginalName($movieOriginalName)
    {
        $this->movieOriginalName = $movieOriginalName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovieTranslatedName()
    {
        return $this->movieTranslatedName;
    }

    /**
     * @param mixed $movieTranslatedName
     * @return $this
     */
    public function setMovieTranslatedName($movieTranslatedName)
    {
        $this->movieTranslatedName = $movieTranslatedName;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjectSlug()
    {
        return $this->projectSlug;
    }

    /**
     * @param string $projectSlug
     * @return $this
     */
    public function setProjectSlug($projectSlug)
    {
        $this->projectSlug = $projectSlug;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return route('workshop::projects::edit', ['project' => $this->getProjectSlug()]);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return $this
     */
    public function setPrivateMode()
    {
        $mode = array_search('private', config('projects.modes'));
        return $this->setMode($mode);
    }

    /**
     * @return $this
     */
    public function setPublicMode()
    {
        $mode = array_search('public', config('projects.modes'));
        return $this->setMode($mode);
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->getMode() == array_search('public', config('projects.modes'));
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->getMode() == array_search('private', config('projects.modes'));
    }

    /**
     * @return string
     */
    public function getOrthography()
    {
        return $this->orthography;
    }

    /**
     * @param string $orthography
     * @return $this
     */
    public function setOrthography($orthography)
    {
        $this->orthography = $orthography;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRipName()
    {
        return $this->ripName;
    }

    /**
     * @param mixed $ripName
     * @return $this
     */
    public function setRipName($ripName)
    {
        $this->ripName = $ripName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsGenerated()
    {
        return $this->isGenerated;
    }

    /**
     * @param mixed $isGenerated
     * @return $this
     */
    public function setIsGenerated($isGenerated)
    {
        $this->isGenerated = $isGenerated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return ReleaseFile|false
     */
    public function getCyrillicFile()
    {
        foreach ($this->getFiles() as $file) {
            if ($file->isCyrillic()) {
                return $file;
            }
        }
        return false;
    }

    /**
     * @return ReleaseFile|false
     */
    public function getLatinFile()
    {
        foreach ($this->getFiles() as $file) {
            if ($file->isLatin()) {
                return $file;
            }
        }
        return false;
    }

    /**
     * @param $abc
     * @return false|ReleaseFile
     */
    public function getFile($abc)
    {
        if (ReleaseFile::CYRILLIC_ABC == $abc) {
            return $this->getCyrillicFile();
        } elseif (ReleaseFile::LATIN_ABC == $abc) {
            return $this->getLatinFile();
        }
        return false;
    }

    /**
     * @param ReleaseFile $file
     * @return $this
     */
    public function removeFile(ReleaseFile $file)
    {
        $this->files->removeElement($file);
        return $this;
    }

    /**
     * @param mixed $files
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetFiles()
    {
        $this->getFiles()->clear();
        $this->setIsGenerated(false);
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function generateFiles($content)
    {
        $charsets =  (array) config('projects.charsets');
        array_walk($charsets, [$this, 'generateFile'], $content);
        $this->setIsGenerated(true);
        return $this;
    }

    /**
     * @param array $charsets
     * @param $abc
     * @param $content
     * @return $this
     */
    public function generateFile($charsets, $abc, $content)
    {
        $fileName = studly_case($this->getRipName() . ' ' . $abc);
        $charsets =  (array) $charsets;
        array_walk($charsets, function($charset) use ($abc, $content, $fileName) {
            foreach (config('projects.subtitles.permitted-formats') as $format) {
                $releaseFile = app()->build(ReleaseFile::class);
                $releaseFile->setName($fileName . '.' . $format);
                $releaseFile->setCharset($charset);
                $releaseFile->setAlphabet($abc);
                $data = $releaseFile->handleData($content, $this->getOrthography());
                $releaseFile->setData($data);
                $this->getFiles()->add($releaseFile);
            }
        });
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function generateCreatedAt()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @ODM\PrePersist
     * @ODM\PreUpdate
     */
    public function generateUpdatedAt()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ODM\PrePersist
     */
    public function importOwner()
    {
        $userId = Auth::id();
        $this->getOwner()->setUserId($userId);
    }

    /**
     * @ODM\PrePersist
     * @param User|null $user
     * @return $this
     */
    public function importMember($user = null)
    {
        if ( ! $user instanceof User) {
            $user = Auth::user();
        }
        $member = app()->build(ReleaseMember::class);
        $member->setUserId($user->getId());
        if ($this->belongsToYou()) {
            $member->setConfirmedState();
        } else {
            $member->setPendingState();
        }
        $this->addMember($member);
        return $this;
    }

    /**
     * @return string
     */
    public function getReadiness()
    {
        return $this->readiness;
    }

    /**
     * @param string $readiness
     * @return $this
     */
    public function setReadiness($readiness)
    {
        $this->readiness = $readiness;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function generateReadiness()
    {
        $this->setReadiness('0%');
    }

    /**
     * @return mixed
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param mixed $members
     * @return $this
     */
    public function setMembers($members)
    {
        $this->members = $members;
        return $this;
    }

    /**
     * @param ReleaseMember $member
     * @return $this
     */
    public function addMember(ReleaseMember $member)
    {
        $this->members->add($member);
        return $this;
    }

    /**
     * @param ReleaseMember $member
     * @return $this
     */
    public function removeMember(ReleaseMember $member)
    {
        $this->members->removeElement($member);
        return $this;
    }

    /**
     * @return array
     */
    public function getConfirmedMembers()
    {
        return array_filter($this->getMembers()->toArray(), function ($member) {
            return $member->isConfirmed();
        });
    }

    /**
     * @return bool
     */
    public function belongsToYou()
    {
        return $this->belongsToUser(Auth::id());
    }

    /**
     * @param string $userId
     * @return bool
     */
    public function belongsToUser($userId)
    {
        return $userId === $this->getOwner()->getUserId();
    }

    /**
     * @return bool
     */
    public function includesYou()
    {
        foreach ($this->getMembers() as $member) {
            if ($member->getUserId() === Auth::id()
                && $member->getState() === ReleaseMember::CONFIRMED_STATE
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        return $this->belongsToYou() || $this->includesYou() || $this->isPublic();
    }
}