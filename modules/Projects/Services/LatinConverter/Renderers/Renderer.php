<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

interface Renderer
{
    public function render($text);

    public function wrap(Renderer $wrapper);

    public function unwrap();

    public function getWrapper();

    public function getText($text);
}