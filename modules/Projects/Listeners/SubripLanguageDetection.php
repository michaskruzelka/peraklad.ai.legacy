<?php

namespace Modules\Projects\Listeners;

use Modules\Projects\Events\CaptionFileWasParsed;
use Modules\Projects\Services\LanguageDetector;

class SubripLanguageDetection
{
    /**
     * @var LanguageDetector
     */
    protected $detector;

    public function __construct(LanguageDetector $detector)
    {
        $this->detector = $detector;
    }

    /**
    * Handle the event.
    *
    * @param CaptionFileWasParsed  $event
    */
    public function handle(CaptionFileWasParsed $event)
    {



        //        $sampleText = '';
//        foreach ($captionFile->getCues() as $index => $cue) {
//            $sampleText .= $cue->getText() . ' ';
//            if ($index > 10) break;
//        }
//        $srtLanguage = $languageDetector->detect($sampleText);
//        var_dump($srtContent);
//        dd($srtLanguage);
    }
}