<?php

namespace App\Traits;

trait ControllerJson
{
    /**
     * @param mixed $dat
     * @return array|string
     */
    protected function utf8EncodeAll($dat)
    {
        if (is_string($dat)) return htmlentities($dat);
        if ( ! is_array($dat)) return $dat;
        $ret = array();
        foreach($dat as $i=>$d) $ret[$i] = $this->utf8EncodeAll($d);
        return $ret;
    }

//    protected function utf8DecodeAll($dat)
//    {
//        if (is_string($dat)) return utf8_decode($dat);
//        if ( ! is_array($dat)) return $dat;
//        $ret = array();
//        foreach($dat as $i=>$d) $ret[$i] = $this->utf8DecodeAll($d);
//        return $ret;
//    }
}