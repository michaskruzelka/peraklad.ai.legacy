<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class DefaultRenderer implements Renderer
{
    /**
     * @var Renderer
     */
    protected $wrapper;

    protected $actions = [];
    protected $actionNames = [];
    protected $letters = [];

    public function __construct()
    {
        foreach ($this->actionNames as $actionName) {
            $this->actions[$actionName] = app()->make(
                'Modules\Projects\Services\LatinConverter\Actions\\' . $actionName
            );
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public final function render($text)
    {
        $text = $this->getText($text);
        if ($wrapper = $this->getWrapper()) {
            $text = $wrapper->render($text);
        }
        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public function getText($text)
    {
        foreach ($this->letters as $letter) {
            foreach ($this->actions as $action) {
                $text = $action->setSearch($letter)->act($text);
            }
        }
        return $text;
    }

    /**
     * @param Renderer $wrapper
     * @return $this
     */
    public function wrap(Renderer $wrapper)
    {
        $this->wrapper = $wrapper;
        return $this;
    }

    /**
     * @return $this
     */
    public function unwrap()
    {
        $this->wrapper = false;
        return $this;
    }

    /**
     * @return Renderer
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }
}