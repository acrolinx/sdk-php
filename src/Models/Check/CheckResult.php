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
    private $document;
    private $quality;
    private $reports;

    public function __construct(ResponseInterface $response)
    {
        $responseBody = json_decode($response->getBody());
        $this->id = $responseBody->data->id;

        $commonCustomFieldStdObj = $responseBody->data->document->customFields[0];
        if(!isset($commonCustomFieldStdObj->displayName)){
            $commonCustomFieldStdObj->displayName = 'testField';
        }
        if(!isset($commonCustomFieldStdObj->possibleValues)){
            $commonCustomFieldStdObj->possibleValues = [];
        }
        $customFieldCommon = new CustomFieldCommon($commonCustomFieldStdObj->displayName,
            $commonCustomFieldStdObj->key, []);

        $this->document = new DocumentDescriptor($responseBody->data->document->id, $customFieldCommon);
        $this->quality = new DocumentQuality($responseBody->data->quality->score, $responseBody->data->quality->status);
        $this->reports = new Report($responseBody->data->reports->scorecard->linkAuthenticated,
            $responseBody->data->reports->scorecard->link);
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


}
