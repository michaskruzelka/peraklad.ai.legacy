<?php

namespace Modules\Projects\Services;

use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Project;

class SlugGenerator
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * SlugGenerator constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    public function generate(Project $project)
    {
        if ( ! $project->getInfo()->getTranslatedTitle()) {
            throw new \Exception('There is no Translated title property in Project document');
        }
        $translatedTitle = $project->getInfo()->getTranslatedTitle();
        $imbdId = $project->getInfo()->getImdbId();
        $slug = str_slug(str_limit($translatedTitle . ' ' . $imbdId, 20), '-');
        $count = $this->dm->getRepository(Project::class)->checkBySlug($slug);
        if ($count > 0 || '' == $slug) {
            $slug .= $project->getCreatedAt()->getTimestamp();
        }
        return $slug;
    }
}