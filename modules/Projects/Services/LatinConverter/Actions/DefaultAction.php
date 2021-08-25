<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

trait DefaultAction
{
    /**
     * @var string
     */
    protected $search;

//    /**
//     * @var array
//     */
//    protected $pairs = [];

    /**
     * @param string $search
     * @return $this
     */
    public function setSearch($search)
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getReplace()
    {
        if ( ! isset($this->pairs[$this->search])) {
            throw new \Exception('The search letter is not correct.');
        }
        return $this->pairs[$this->search];
    }
}