<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ZzRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ж', 'ж'];
}
