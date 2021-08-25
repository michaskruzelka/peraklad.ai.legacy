<?php

namespace Modules\Projects\Services\ImdbImporter;

use Modules\Projects\Contracts\ImdbImporter;
use Modules\Projects\Entities\ProjectInfo;
use GuzzleHttp\Client;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Language;
use GuzzleHttp\Exception\RequestException;
use Log;

class OmdbApi implements ImdbImporter
{
    const URL = 'http://www.omdbapi.com';
    const UNDEFINED_VALUE = 'N/A';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * OmdbApi constructor.
     * @param Client $httpClient
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(Client $httpClient, LaravelDocumentManager $ldm)
    {
        $this->httpClient = $httpClient;
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * @param $projectInfo ProjectInfo
     * @return boolean
     */
    public function import(ProjectInfo $projectInfo)
    {
        $content = $this->getContent($projectInfo);
        if (is_null($content)) {
            return false;
        }
        $this->importOriginalTitle($projectInfo, $content)
            ->importImdbId($projectInfo, $content)
            ->importImdbRating($projectInfo, $content)
            ->importPlot($projectInfo, $content)
            ->importPoster($projectInfo, $content)
            ->importLanguage($projectInfo, $content)
            ->importYear($projectInfo, $content)
            ->importType($projectInfo, $content)
            ->importEpisodes($projectInfo, $content)
        ;
        return true;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param $content
     * @return $this
     */
    protected function importEpisodes(ProjectInfo $projectInfo, $content)
    {
        $garbage = $projectInfo->getGarbage();
        $episodes = null;
        if (isset($content->imdbID)
            && self::UNDEFINED_VALUE != $content->imdbID
            && (isset($content->Type) && 'series' == $content->Type)
        ) {
            $season = 1;
            do {
                $query = [
                    'r' => 'json',
                    'i' => $content->imdbID,
                    'Season' => $season
                ];
                $seriesContent = $this->request($query);
                if (isset($seriesContent->Episodes) && is_array($seriesContent->Episodes)) {
                    $episodes = (array) $episodes;
                    array_walk($seriesContent->Episodes, function($episode) use(&$episodes, $season) {
                        if ( ! isset($episode->Title)
                            ||  ! isset($episode->Released)
                            ||  ! isset($episode->Episode)
                            ||  ! isset($episode->imdbID)
                            || self::UNDEFINED_VALUE == $episode->Title
                            || self::UNDEFINED_VALUE == $episode->Episode
                            || self::UNDEFINED_VALUE == $episode->Released
                            || self::UNDEFINED_VALUE == $episode->imdbID
                        ) {
                            return false;
                        }
                        $released = new \DateTime($episode->Released);
                        $episodes[] = [
                            'season' => $season,
                            'episode' => (int) $episode->Episode,
                            'originalTitle' => $episode->Title,
                            'year' => $released->format('Y'),
                            'imdbId' => $this->removeIdPrefix($episode->imdbID)
                        ];
                    });
                }
                $season++;
            } while ($seriesContent);
        }
        $garbage['episodes'] = $episodes;
        $projectInfo->setGarbage($garbage);
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importType(ProjectInfo $projectInfo, $content)
    {
        $garbage = $projectInfo->getGarbage();
        if (isset($content->Type) && self::UNDEFINED_VALUE != $content->Type) {
            $garbage['type'] = $content->Type;
        } else {
            $garbage['type'] = null;
        }
        $projectInfo->setGarbage($garbage);
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importLanguage(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->Language) && self::UNDEFINED_VALUE != $content->Language) {
            $content->Language = explode(', ', $content->Language);
            $content->Language = array_shift($content->Language);
            $language = $this->dm
                ->getRepository(Language::class)
                ->findOneBy(['englishName' => $content->Language, 'isSubable' => true])
            ;
            if ( ! is_null($language)) {
                $projectInfo->getLanguage()->setIso6393b($language->getId());
            }
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importYear(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->Year) && self::UNDEFINED_VALUE != $content->Year) {
            $content->Year = explode('–', $content->Year);
            $content->Year = array_shift($content->Year);
            $projectInfo->setYear($content->Year);
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importPlot(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->Plot) && self::UNDEFINED_VALUE != $content->Plot) {
            $projectInfo->setPlot($content->Plot);
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importImdbRating(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->imdbRating) && self::UNDEFINED_VALUE != $content->imdbRating) {
            $projectInfo->setImdbRating($content->imdbRating);
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importImdbId(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->imdbID) && self::UNDEFINED_VALUE != $content->imdbID) {
            $projectInfo->setImdbId($this->removeIdPrefix($content->imdbID));
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importOriginalTitle(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->Title) && self::UNDEFINED_VALUE != $content->Title) {
            $projectInfo->setOriginalTitle($content->Title);
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @param object $content
     * @return $this
     */
    protected function importPoster(ProjectInfo $projectInfo, $content)
    {
        if (isset($content->Poster) && self::UNDEFINED_VALUE != $content->Poster) {
            try {
                $response = $this->httpClient->request('GET', $content->Poster);
                $imgContent = $response->getBody()->getContents();
                $base64 = "data:{$response->getHeader('content-type')[0]};base64," . base64_encode($imgContent);
                $projectInfo->getPoster()->setRowData($base64);
            } catch (RequestException $e) {
                Log::info($e->getMessage());
            }
        }
        return $this;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @return object|null
     */
    protected function getContent(ProjectInfo $projectInfo)
    {
        $query = [
            'plot' => 'short',
            'r' => 'json'
        ];
        $imdbId = $projectInfo->getImdbId();
        if ( ! is_null($imdbId)) {
            $query['i'] = $this->checkIdPrefix($imdbId) ? $imdbId : $this->addIdPrefix($imdbId);
            if ( ! $content = $this->request($query)) {
                $projectInfo->setImdbId(null);
                return $this->getContent($projectInfo);
            }
            return $content;
        } elseif ( ! is_null($projectInfo->getOriginalTitle())) {
            $query['t'] = $projectInfo->getOriginalTitle();
            if ($content = $this->request($query)) {
                return $content;
            }
        }
        return null;
    }

    /**
     * @param $query array
     * @return mixed(bool|stdClass)
     */
    protected function request(array $query)
    {
        $config = $this->getConfig();
        $config['query'] = $query;
        $response = $this->httpClient->get(self::URL, $config);
        if ($response->getStatusCode() == 200) {
            $content = json_decode($response->getBody()->getContents());
            if (isset($content->Response) && $content->Response == 'True') {
                return $content;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        return [
            'timeout' => 5,
            'allow_redirects' => false
        ];
    }

    /**
     * @param $imdbId
     * @return mixed
     */
    protected function removeIdPrefix($imdbId)
    {
        return str_replace('tt', '', $imdbId);
    }

    /**
     * @param $imdbId
     * @return bool
     */
    protected function checkIdPrefix($imdbId)
    {
        return strpos($imdbId, 'tt') !== false;
    }

    /**
     * @param $imdbId
     * @return string
     */
    protected function addIdPrefix($imdbId)
    {
        return 'tt' . $imdbId;
    }
}