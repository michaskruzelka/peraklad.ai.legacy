<?php

namespace Modules\Users\Services\GenderDetector;

use Modules\Users\Contracts\GenderDetector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GenderApiCom implements GenderDetector
{
    const URL = 'https://gender-api.com/get';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * GenderApiCom constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     * @return string ('null'|'female'|'male')
     */
    public function detect($name)
    {
        $myKey = config('users.genderDetectorApiKey');
        $names = explode(' ', $name);
        foreach ($names as $name) {
            $query = [
                'key' => $myKey,
                'name' => $name
            ];
            if ($data =  $this->request($query)) {
                return $data->gender;
            }
        }
        return 'null';
    }

    /**
     * @param $query array
     * @return mixed(bool|stdClass)
     */
    protected function request(array $query)
    {
        $config = $this->getConfig();
        $config['query'] = $query;
        try {
            $response = $this->httpClient->get(self::URL, $config);
        } catch (RequestException $e) {
            \Log::warning($e->getMessage());
            return false;
        }
        if ($response->getStatusCode() == 200) {
            $content = json_decode($response->getBody()->getContents());
            if (isset($content->gender) && "unknown" != $content->gender) {
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
            'timeout' => 3,
            'allow_redirects' => false
        ];
    }
}
