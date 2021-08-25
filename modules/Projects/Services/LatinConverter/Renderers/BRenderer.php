<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class BRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Б', 'б'];
}
