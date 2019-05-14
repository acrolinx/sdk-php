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


class CheckResult
{

    private $id;
    private $document;
    private $quality;
    private $reports;

    public function __construct(string $id, DocumentDescriptor $documentDescriptor,
                                DocumentQuality $documentQuality, CheckResultReports $reports)
    {
        $this->id = $id;
        $this->document = $documentDescriptor;
        $this->quality = $documentQuality;
        $this->reports = $reports;
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
     * @return CheckResultReports
     */
    public function getReports(): CheckResultReports
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