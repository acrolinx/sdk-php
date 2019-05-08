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

namespace Acrolinx\SDK\Models;

// ToDo: I have currently kept access specifiers for data members as public for converting objects to JSON.
// ToDo: We need to write a recursive function to convert objects to JSON which converts private members.

use Acrolinx\SDK\Utils\SDKUtils;

class CheckRequest
{
    public $content;
    public $contentEncoding;
    public $checkOptions;
    public $document;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function getJson()
    {
        return json_encode(SDKUtils::objectToArray($this));
    }

}
