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


use http\Url;

class Report
{
    private $linkAuthenticated;
    private $link;

    public function __construct(string $linkAuthenticated, string $link)
    {
        $this->linkAuthenticated = $linkAuthenticated;
        $this->link = $link;
    }

    /**
     * @return Url
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return Url
     */
    public function getLinkAuthenticated(): string
    {
        return $this->linkAuthenticated;
    }
}