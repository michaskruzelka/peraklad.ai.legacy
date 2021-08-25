<?php

namespace Modules\Projects\Services\SubtitlesApi;

use Modules\Projects\Contracts\SubtitlesApi;

class Opensubtitles implements SubtitlesApi
{
    const URL = 'http://api.opensubtitles.org/xml-rpc';

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $defaultLang;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @var string
     */
    protected $token;

    /**
     * Opensubtitles constructor.
     */
    public function __construct()
    {
        $this->username = config('projects.subtitlesApi.username');
        $this->password = config('projects.subtitlesApi.password');
        $this->defaultLang = config('projects.subtitlesApi.language');
        $this->userAgent = config('projects.subtitlesApi.useragent');
        $this->login();
    }

    /**
     * Opensubtitles destructor
     */
    public function __destruct()
    {
        $this->logout();
    }

    /**
     * @param string $lang
     * @param string|null $imdbId
     * @param string|null $title
     * @return boolean|array
     */
    public function search($lang, $imdbId, $title)
    {
        $query = ['sublanguageid' => $lang];
        if ($imdbId) {
            $query['imdbid'] = $this->removeIdPrefix($imdbId);
        } elseif ($title) {
            $query['query'] = $title;
        } else {
            return false;
        }

        $request  = xmlrpc_encode_request(
            "SearchSubtitles",
            [$this->token, [$query], ['limit' => 50]]
        );

        if ( ! $response = $this->request($request)) {
            return false;
        }

        $availableFormats = config('projects.subtitles.permitted-formats');
        $subtitles = array_filter($response['data'], function($subtitle) use($availableFormats) {
            return (
                in_array($subtitle['SubFormat'], $availableFormats)
                    //&& ! in_array($subtitle['SubEncoding'], ['Unknown', ''])
            );
        });

        $subtitles = array_map(function($subtitle) {
            return [
                'MatchedBy' => $subtitle['MatchedBy'],
                'IDSubtitleFile' => $subtitle['IDSubtitleFile'],
                'MovieName' => $subtitle['MovieName'] . ' (' . $subtitle['MovieYear'] . ')',
                'MovieReleaseName' => $subtitle['MovieReleaseName'],
                'SubRating' => $subtitle['SubRating'],
                'SubEncoding' => $subtitle['SubEncoding'],
                //'SubDownloadLink' => $subtitle['SubDownloadLink']
            ];
        }, $subtitles);

        return array_values($subtitles);
    }

    /**
     * @param string $subtitleId
     * @return string
     * @throws \Exception
     */
    public function download($subtitleId)
    {
        $request  = xmlrpc_encode_request(
            "DownloadSubtitles",
            [$this->token, [$subtitleId]]
        );

        if ( ! $response = $this->request($request)) {
            throw new \Exception('Could not download subtitle');
        }

        $file = current($response['data'])['data'];
        return gzdecode(base64_decode($file));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function login()
    {
        $request  = xmlrpc_encode_request(
            "LogIn",
            [$this->username, $this->password, $this->defaultLang, $this->userAgent]
        );
        if ($response = $this->request($request)) {
            $this->token = $response['token'];
        }
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function logout()
    {
        $request  = xmlrpc_encode_request(
            "LogOut",
            [$this->token]
        );
        $this->request($request);
        return $this;
    }

    /**
     * @param $xmlRequest string
     * @return mixed(bool|stdClass|array)
     * @throws \Exception
     */
    protected function request($xmlRequest)
    {
        $context  = stream_context_create([
            'http' => [
                'method'  => "POST",
                'header'  => "Content-Type: text/xml",
                'content' => $xmlRequest
            ]
        ]);
        $file     = file_get_contents(self::URL, false, $context);
        $response = xmlrpc_decode($file);

        if (($response && xmlrpc_is_fault($response))) {
            $message = "xmlrpc: {$response['faultString']} ({$response['faultCode']})";
            \Log::info($message);
            throw new \Exception($message);
        }

        if ($this->isWrongStatus($response)) {
            $message = 'Bad xmlrpc request: ' . print_r($response, true);
            \Log::info($message);
            return false;
        }

        return $response;
    }

    /**
     * @param array $response
     * @return bool
     */
    private function isWrongStatus($response)
    {
        return (empty($response['status']) || $response['status'] != '200 OK');
    }

    /**
     * @param $imdbId
     * @return mixed
     */
    protected function removeIdPrefix($imdbId)
    {
        return str_replace('tt', '', $imdbId);
    }
}