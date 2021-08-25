<?php

namespace Modules\Users\Contracts;

interface Geocoder
{
    /**
     * @param $lng
     * @param $lat
     * @return mixed
     */
    public function getAddress($lng, $lat);

    /**
     * @param $address
     * @return mixed
     */
    public function getCoordinates($address);
}