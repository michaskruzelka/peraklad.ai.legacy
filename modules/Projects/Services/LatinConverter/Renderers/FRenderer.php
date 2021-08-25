<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class FRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ф', 'ф'];
}
