<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class PRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['П', 'п'];
}
