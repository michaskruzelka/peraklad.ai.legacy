<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class SsRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ш', 'ш'];
}
