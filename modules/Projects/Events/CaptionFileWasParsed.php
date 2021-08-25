<?php

namespace Modules\Projects\Events;

use App\Events\Event;
use Captioning\File as CaptionFile;

class CaptionFileWasParsed extends Event
{
    protected $captionFile;

    /**
     * CaptionFileWasParsed constructor.
     * @param CaptionFile $captionFile
     */
    public function __construct(CaptionFile $captionFile)
    {
        $this->captionFile = $captionFile;
    }

    /**
     * @return CaptionFile
     */
    public function getCaptionFile()
    {
        return $this->captionFile;
    }
}