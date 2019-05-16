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



class CheckingCapabilities
{
    private $guidanceProfiles = array();
    private $contentFormats = array();
    private $contentEncodings;
    private $checkTypes = array();
    private $reportTypes = array();
    private $referencePattern;

    public function __construct($checking)
    {
        foreach ($checking->guidanceProfiles as $guidanceProfile) {
            array_push($this->guidanceProfiles, new GuidanceProfile($guidanceProfile));
        }

        foreach ($checking->contentFormats as $contentFormat) {
            array_push($this->contentFormats, new ContentFormat($contentFormat));
        }
        $this->contentEncodings = $checking->contentEncodings;
        $this->checkTypes = $checking->checkTypes;
        $this->reportTypes = $checking->reportTypes;
        $this->referencePattern = $checking->referencePattern;

    }

    /**
     * @return ReportType[]
     */
    public function getReportTypes(): array
    {
        return $this->reportTypes;
    }

    /**
     * @return CheckType[]
     */
    public function getCheckTypes(): array
    {
        return $this->checkTypes;
    }

    /**
     * @return ContentEncoding[]
     */
    public function getContentEncodings()
    {
        return $this->contentEncodings;
    }

    /**
     * @return array
     */
    public function getContentFormats(): array
    {
        return $this->contentFormats;
    }

    /**
     * @return GuidanceProfile[]
     */
    public function getGuidanceProfiles(): iterable
    {
        return $this->guidanceProfiles;
    }

}

class ContentFormat
{
    private $id;
    private $displayName;

    public function __construct($contentFormat)
    {
        $this->id = $contentFormat->id;
        $this->displayName = $contentFormat->displayName;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
