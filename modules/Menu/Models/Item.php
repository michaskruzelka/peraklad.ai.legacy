<?php

namespace Modules\Menu\Models;

class Item
{
    /**
     * @var array
     */
    protected $map;

    /**
     * @var array
     */
    protected $children = [];

    /**
     * @var string
     */
    protected $class = '';

    /**
     * Item constructor.
     * @param array $map
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
        $this->initChildren();
    }

    /**
     * @return mixed (string|null)
     */
    public function getTitle()
    {
        return $this->getProperty('title');
    }

    /**
     * @return mixed (string|null)
     */
    public function getUrl()
    {
        $routes = $this->getProperty('routes');
        if (is_array($routes) &&  ! empty($routes)) {
            return route(head($routes));
        }
        return null;
    }

    /**
     * @return mixed (string|null)
     */
    public function getCategory()
    {
        return $this->getProperty('category');
    }

    /**
     * @param string $name
     *
     * @return mixed (string|null)
     */
    public function getProperty($name)
    {
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !! count($this->children);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $routes = $this->getProperty('routes');
        if (is_array($routes) && in_array(\Request::route()->getName(), $routes)) {
            return true;
        }
        foreach($this->getChildren() as $child) {
            if ($child->isActive()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function addClass($class)
    {
        $this->class .= " {$class}";
        return $this;
    }

    /**
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function initChildren()
    {
        if (empty($this->map['children']) ||  ! is_array($this->map['children'])) {
            return $this;
        }
        foreach ($this->map['children'] as $config) {
            $this->children[] = app()->build('Modules\Menu\Models\Item', array($config));
        }
        return $this;
    }
}