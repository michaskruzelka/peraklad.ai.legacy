<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class LAcademicRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['PalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Л', 'л'];
}
