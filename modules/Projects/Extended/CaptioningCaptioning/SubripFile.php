<?php

namespace Modules\Projects\Extended\CaptioningCaptioning;

use Captioning\File;
use Captioning\Format\SubripCue;

class SubripFile extends File
{
    const PATTERN = '/^[\p{C}]{0,3}[\d]+((?:\r\n|\r|\n))[\d]{2}:[\d]{2}:[\d]{2}(?:,|\.)[\d]{3}[ ]-->[ ][\d]{2}:[\d]{2}:[\d]{2}(?:,|\.)[\d]{3}(?:\1[\S ]+)+(?:\1\1(?<=\r\n|\r|\n)[\d]+\1[\d]{2}:[\d]{2}:[\d]{2},[\d]{3}[ ]-->[ ][\d]{2}:[\d]{2}:[\d]{2},[\d]{3}(?:\1[\S ]+)+)*\1?/xum';

    private $defaultOptions = array('_stripTags' => false, '_stripBasic' => false, '_replacements' => false);

    private $options = array();

    public function __construct($_filename = null, $_encoding = null, $_useIconv = true)
    {
        parent::__construct($_filename, $_encoding, $_useIconv);
        $this->options = $this->defaultOptions;
    }

    public function parse()
    {
        $matches = array();
        $res = preg_match(self::PATTERN, $this->fileContent, $matches);

        if ($res === false || $res === 0) {
            throw new \Exception('Некарэктны srt файл.');
        }

        $this->setLineEnding($matches[1]);
        $bom = pack('CCC', 0xef, 0xbb, 0xbf);
        $matches = explode($this->lineEnding.$this->lineEnding, trim($matches[0], $bom.$this->lineEnding));

        $subtitleOrder = 1;
        $subtitleTime = '';

        foreach ($matches as $match) {
            $subtitle = explode($this->lineEnding, $match, 3);
            $timeline = explode(' --> ', $subtitle[1]);

            $subtitleTimeStart = $timeline[0];
            $subtitleTimeEnd = $timeline[1];

            if ( ! $this->validateTimelines($subtitleTime, $subtitleTimeStart, true)) {
                $message = "The timeline is not valid: newTimeStart ({$subtitleTimeStart}) < prevTimeEnd
                    ({$subtitleTime}). Text: {$subtitle[2]}"
                ;
                \Log::warning($message);
                $timeline[0] = $subtitleTime;
                $subtitleTimeStart = $timeline[0];
            }

            if ( ! $this->validateTimelines($subtitleTimeStart, $subtitleTimeEnd)) {
                $message = "The timeline is not valid: newTimeEnd ({$subtitleTimeEnd}) < newTimeStart
                    ({$subtitleTimeStart}). Text: {$subtitle[2]}"
                ;
                \Log::warning($message);
                $timeline[1] = $subtitleTimeStart;
                $subtitleTimeEnd = $timeline[1];
            }

            $subtitleTime = $subtitleTimeEnd;
            $cue = new SubripCue($timeline[0], $timeline[1], $subtitle[2]);
            $cue->setLineEnding($this->lineEnding);
            $this->addCue($cue);
            $subtitleOrder++;
        }

        return $this;
    }

    public function build()
    {
        if ($this->getCuesCount() > 0) {
            $this->buildPart(0, $this->getCuesCount() - 1);
        }
        return $this;
    }

    public function buildPart($_from, $_to)
    {
        $this->sortCues();

        $i = 1;
        $buffer = "";
        if ($_from < 0 || $_from >= $this->getCuesCount()) {
            $_from = 0;
        }

        if ($_to < 0 || $_to >= $this->getCuesCount()) {
            $_to = $this->getCuesCount() - 1;
        }

        for ($j = $_from; $j <= $_to; $j++) {
            $cue = $this->getCue($j);
            $buffer .= $i.$this->lineEnding;
            $buffer .= $cue->getTimeCodeString().$this->lineEnding;
            $buffer .= $cue->getText(
                $this->options['_stripTags'],
                $this->options['_stripBasic'],
                $this->options['_replacements']
            );
            $buffer .= $this->lineEnding;
            $buffer .= $this->lineEnding;
            $i++;
        }

        $this->fileContent = $buffer;

        return $this;
    }

    /**
     * @param array $options array('_stripTags' => false, '_stripBasic' => false, '_replacements' => false)
     * @return SubripFile
     * @throws \UnexpectedValueException
     */
    public function setOptions(array $options)
    {
        if (!$this->validateOptions($options)) {
            throw new \UnexpectedValueException('Options consist not allowed keys');
        }
        $this->options = array_merge($this->defaultOptions, $options);
        return $this;
    }

    /**
     * @return SubripFile
     */
    public function resetOptions()
    {
        $this->options = $this->defaultOptions;
        return $this;
    }

    /**
     * @param array $options
     * @return bool
     */
    private function validateOptions(array $options)
    {
        foreach (array_keys($options) as $key) {
            if (!array_key_exists($key, $this->defaultOptions)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $startTimeline
     * @param string $endTimeline
     * @param boolean $allowEqual
     * @return boolean
     */
    private function validateTimelines($startTimeline, $endTimeline, $allowEqual = false)
    {
        $startDateTime = \DateTime::createFromFormat('H:i:s,u', $startTimeline);
        $endDateTime = \DateTime::createFromFormat('H:i:s,u', $endTimeline);

        // If DateTime objects are equals need check milliseconds precision.
        if ($startDateTime == $endDateTime) {
            $startSeconds = $startDateTime->getTimestamp();
            $endSeconds = $endDateTime->getTimestamp();

            $startMilliseconds = ($startSeconds * 1000) + (int)substr($startTimeline, 9);
            $endMilliseconds = ($endSeconds * 1000) + (int)substr($endTimeline, 9);

            return $startMilliseconds < $endMilliseconds || ($allowEqual && $startMilliseconds === $endMilliseconds);
        }

        return $startTimeline < $endTimeline;
    }

    protected function encode()
    {
        if ($this->useIconv) {
            $this->fileContent = iconv($this->encoding, 'UTF-8//IGNORE', $this->fileContent);
        } else {
            $this->fileContent = mb_convert_encoding($this->fileContent, 'UTF-8//IGNORE', $this->encoding);
        }
    }
}
