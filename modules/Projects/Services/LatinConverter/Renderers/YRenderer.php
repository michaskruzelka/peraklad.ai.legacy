<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class YRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ы', 'ы'];
}
