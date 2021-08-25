<?php

namespace Modules\Projects\Contracts;

use Modules\Projects\Entities\ProjectInfo;

interface ImdbImporter
{
    /**
     * @param $projectInfo ProjectInfo
     * @return mixed
     */
    public function import(ProjectInfo $projectInfo);
}