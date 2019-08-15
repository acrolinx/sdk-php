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


class AcrolinxEndPointProperties
{

    public $clientSignature = '';
    public $clientLocale = 'en';
    public $platformUrl = '';
    public $clientVersion = 'unknown';

    /**
     * AcrolinxEndPointProperties constructor.
     * @param $clientSignature Signature of integration
     * @param $platformUrl Acrolinx Platform URL to connect to
     * @param $clientLocale Clients locale
     * @param $clientVersion Version of host application
     */
    public function __construct($clientSignature, $platformUrl, $clientLocale, $clientVersion)
    {
        $this->clientVersion = $clientVersion;
        $this->clientLocale = $clientLocale;
        $this->platformUrl = rtrim($platformUrl, '/');
        $this->clientSignature = $clientSignature;
    }

}
