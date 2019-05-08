<?php


namespace Acrolinx\SDK;


class SDKUtils
{
    public static function objectToArray($obj) {
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? SDKUtils::objectToArray((object)$val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }
}