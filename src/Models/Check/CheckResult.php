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


use Psr\Http\Message\ResponseInterface;

class CheckResult
{

    private $id;
    private $quality;
    private $reports;
    private $responseBody;

    public function __construct(ResponseInterface $response)
    {
        $this->responseBody = json_decode($response->getBody());
        $this->id = $this->responseBody->data->id;

        $this->quality = new DocumentQuality($this->responseBody->data->quality->score, $this->responseBody->data->quality->status);
        $this->reports = new Report($this->responseBody->data->reports->scorecard->linkAuthenticated,
            $this->responseBody->data->reports->scorecard->link);
    }

    /**
     * @return DocumentDescriptor
     */
    public function getDocument(): DocumentDescriptor
    {
        return $this->document;
    }

    /**
     * @return DocumentQuality
     */
    public function getQuality(): DocumentQuality
    {
        return $this->quality;
    }

    /**
     * @return Report
     */
    public function getReports(): Report
    {
        return $this->reports;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRawResponse(): array
    {
        return $this->responseBody;
    }


}
