<?php

namespace App\Traits;

use Illuminate\View\View;

trait ComposerAdvanced
{
    /**
     * @param View $view
     * @param array $viewsMap
     *
     * @return $this
     */
    public function plug(View $view, array $viewsMap)
    {
        array_walk($viewsMap, array($this, 'plugIterate'), $view);
        return $this;
    }

    /**
     * @param array $config
     * @param string $position
     * @param View $view
     *
     * @return $this
     */
    protected function plugIterate(array $config, $position, View $view)
    {
        $this->composeCallback($config);
        $this->bind($view, $config, $position);
        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    protected function composeCallback(array &$config)
    {
        $config['view'] = call_user_func(array($this, $config['callback']), $config);
        return $this;
    }

    /**
     * @param View $view
     * @param array $config
     * @param string $position
     *
     * @return $this
     */
    protected function bind(View $view, array $config, $position)
    {
        $var = $position . 'Modules';
        $modules = empty($view->$var) ? [] : $view->$var;
        $modules[$config['sort']] = $config['view'];
        ksort($modules);
        $view->$var = $modules;
        return $this;
    }
}