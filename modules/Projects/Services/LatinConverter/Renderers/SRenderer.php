<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class SRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['PalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['С', 'с'];
}
