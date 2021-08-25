<?php

namespace App\Extended\PingpongThemes;

class Repository extends \Pingpong\Themes\Repository
{
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param  string  $file
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function elixir($file)
    {
        $pathToManifest = "{$this->publicPath()}/build/rev-manifest.json";
        $manifest = json_decode(file_get_contents($pathToManifest), true);
        if (isset($manifest[$file])) {
            return "{$this->publicUrl()}/build/{$manifest[$file]}";
        }
        throw new \InvalidArgumentException("File {$file} not defined in asset manifest.");
    }

    /**
     * Get the public path to the current theme
     */
    public function publicPath()
    {
        return public_path("themes/{$this->getCurrent()}");
    }

    /**
     * Get the public url of the current theme
     */
    public function publicUrl()
    {
        return url("themes/{$this->getCurrent()}");
    }

    /**
     * Get layout path
     *
     * @return string
     */
    public function layoutPath()
    {
        return "{$this->getCurrent()}::layouts.{$this->config('layout')}";
    }
}