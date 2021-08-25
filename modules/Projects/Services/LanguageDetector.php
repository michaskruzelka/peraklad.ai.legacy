<?php

namespace Modules\Projects\Services;

use DetectLanguage\DetectLanguage;

class LanguageDetector
{
    public function __construct()
    {
        DetectLanguage::setApiKey(config('projects.languageDetector.api-key'));
    }

    public function detect($content)
    {
        return DetectLanguage::simpleDetect($content);
    }

    public function isBel($content)
    {
        return 'be' === $this->detect($content);
    }
}