<?php

namespace Modules\Projects\Repositories;

use Doctrine\ODM\MongoDB\DocumentRepository;

class Languages extends DocumentRepository
{
    /**
     * @return array
     */
    public function findSubable()
    {
        return $this->findBy(['sub' => true], ['be'=>'asc']);
    }

    /**
     * @param array $languages
     * @return array
     */
    public function sort(array $languages)
    {
        $languages = array_sort($languages, function($language, $key) {
            // Modules\Projects\Entities\Language $language
            $config = config('projects.languagesOrder');
            if (array_key_exists($language->getId(), $config)) {
                return $config[$language->getId()];
            }
            return $key+$this->getTopNumber();
        });
        return $languages;
    }

    /**
     * @return int
     */
    public function getTopNumber()
    {
        $config = config('projects.languagesOrder');
        return count($config);
    }
}