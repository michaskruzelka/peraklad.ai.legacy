<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class NRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['PalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Н', 'н'];
}
