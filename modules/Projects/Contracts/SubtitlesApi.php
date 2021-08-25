<?php

namespace Modules\Projects\Contracts;

interface SubtitlesApi
{
    /**
     * @param string $lang
     * @param string|null $imdbId
     * @param string|null $title
     * @return boolean|array
     */
    public function search($lang, $imdbId, $title);

    /**
     * @param $subtitleId
     * @return mixed
     */
    public function download($subtitleId);
}