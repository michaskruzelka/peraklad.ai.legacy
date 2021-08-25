<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ERenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['VowelPalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Э', 'э'];
}
