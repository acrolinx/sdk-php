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

namespace Acrolinx\SDK\Models\Check;


use Psr\Http\Message\ResponseInterface;

class ContentAnalysisDashboardLinks
{

    private $withoutAccessToken;
    private $withAccessToken;
    private $shortWithAccessToken;
    private $shortWithoutAccessToken;

    public function __construct(ResponseInterface $response)
    {
        $responseBody = json_decode($response->getBody());
        $links = $responseBody->data->links;


        foreach ($links as $link) {
            if ($link->linkType == 'withAccessToken') {
                $this->withAccessToken = $link->link;
            }

            if ($link->linkType == 'withoutAccessToken') {
                $this->withoutAccessToken = $link->link;
            }

            if ($link->linkType == 'shortWithAccessToken') {
                $this->shortWithAccessToken = $link->link;
            }

            if ($link->linkType == 'shortWithoutAccessToken') {
                $this->shortWithoutAccessToken = $link->link;
            }
        }
    }

    /**
     * @return string
     */
    public function getShortWithAccessToken()
    {
        return $this->shortWithAccessToken;
    }

    /**
     * @return string
     */
    public function getWithAccessToken()
    {
        return $this->withAccessToken;
    }

    /**
     * @return string
     */
    public function getShortWithoutAccessToken()
    {
        return $this->shortWithoutAccessToken;
    }

    /**
     * @return string
     */
    public function getWithoutAccessToken()
    {
        return $this->withoutAccessToken;
    }

}
