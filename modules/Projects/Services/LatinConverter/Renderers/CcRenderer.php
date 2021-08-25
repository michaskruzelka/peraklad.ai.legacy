<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class CcRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ч', 'ч'];
}
