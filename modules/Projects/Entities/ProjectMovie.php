<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class ProjectMovie extends Project
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'movie';
    }
}