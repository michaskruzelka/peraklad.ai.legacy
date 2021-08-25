<?php

namespace Modules\Users\Services\Geocoder;

use Modules\Users\Contracts\Geocoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Google implements Geocoder
{
    const URL = 'http://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Google constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param $lng
     * @param $lat
     * @return bool|Response
     */
    public function getAddress($lng, $lat)
    {
        $params = [
            'query' => ['latlng' => $lat . ',' . $lng]
        ];
        return $this->request($params);
    }

    /**
     * @param $address
     * @return bool|Response
     */
    public function getCoordinates($address)
    {
        $params =  [
            'query' => ['address' => $address]
        ];
        return $this->request($params);
    }

    /**
     * @param array $params
     * @return bool|Response
     */
    protected function request(array $params)
    {
        try {
            $response = $this->httpClient->get(self::URL, $params);
        } catch (RequestException $e) {
            \Log::warning($e->getMessage());
            return false;
        }
        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody());
            switch ($body->status) {
                case "ZERO_RESULTS":
                case "OVER_QUERY_LIMIT":
                case "REQUEST_DENIED":
                case "INVALID_REQUEST":
                case "UNKNOWN_ERROR":
                    return false;
                case "OK":
                    return new Response($body);
            }
        }
        return false;
    }
}