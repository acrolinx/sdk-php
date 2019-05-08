<?php

/*
* Copyright 2019-present Acrolinx GmbH
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

namespace Acrolinx\SDK;


class SDKUtils
{
    public static function objectToArray($obj) {
        $obj = (object) array_filter((array) $obj);
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? SDKUtils::objectToArray((object)$val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }
}