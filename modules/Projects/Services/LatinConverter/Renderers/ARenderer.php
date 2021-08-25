<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ARenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['VowelPalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['А', 'а'];
}
