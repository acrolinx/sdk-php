<?php namespace Acrolinx\SDK;

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

class AcrolinxEndpoint
{
    /**  @var string $serverAddress set the Platform URL to talk to */
    private $serverAddress = '';
    private $clientLocale = 'en';
    private $props = null;

    /**
     * AcrolinxEndpoint constructor.
     * @param $props
     */
    public function __construct(AcrolinxEndPointProps $props)
    {
        $this->props = $props;
    }

    /**
     * @param $clientLocale
     * Sets the language interface.
     */
    public function setClientLocale($clientLocale): void
    {
        $this->clientLocale = $clientLocale;
    }

    /**
     * Get server information
     */
    public function getServerInfo()
    {
        return $this->getData('/api/v1/', null, null);

    }

    /**
     * @param SsoSignInoptions $options
     * @return array
     */
    public function signIn(SsoSignInoptions $options)
    {
        return $this->postData('/api/v1/auth/sign-ins', null, null, $options);

    }

    /**
     * @param $authToken
     */
    public function getCapabilities($authToken)
    {

    }

    /**
     * @param string $authToken
     * @return array
     */
    public function getCommonHeaders($authToken)
    {
        $headers = array(
            'X-Acrolinx-Client: ' . $this->props->clientSignature,
            'X-Acrolinx-Client-Locale: ' . $this->props->clientLocale,
            'X-Acrolinx-Base-Url:' . $this->props->baseUrl,
            'Content-Type: application/json'
        );

        if (!is_null($this->props->clientLocale)) {
            array_push($headers, 'X-Acrolinx-Client-Locale: ' . $this->props->clientLocale);
        }

        if (!is_null($authToken)) {
            array_push($headers, 'X-Acrolinx-Auth: ' . $authToken);
        }

        return $headers;

    }

    public function getSsoRequestHeaders(SsoSignInoptions $options): array
    {
        return array(
            $options->getUsernameKey() . ': ' . $options->getUserId(),
            $options->getPasswordKey() . ': ' . $options->getPassword()
        );

    }



    /**
     * @param $path
     * @param $authToken
     * @param $data
     * @return array
     */
    public function postData(string $path, $authToken, $data, SsoSignInoptions $options)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_URL, $this->props->serverAddress . $path);
        $headers = $this->getCommonHeaders($authToken);
        if (!is_null($options)) {
            $ssoHeaders = $this->getSsoRequestHeaders($options);
            $headers = array_merge($headers, $ssoHeaders);

        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $output = array("response" => $result, "status" => $httpStatus);

        curl_close($curl);
        return $output;

    }

    public function getData(string $path, $data, $authToken)
    {
        $curl = curl_init();


        if ($data) {
            $path = sprintf("%s?%s", $path, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, $this->props->serverAddress . $path);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getCommonHeaders($authToken));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $output = array("response" => $result, "status" => $httpStatus);

        curl_close($curl);
        return $output;

    }

    public function putData(string $path, string $authToken, $data)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_URL, $this->props->serverAddress . $path);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getCommonHeaders($authToken));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $output = array("response" => $result, "status" => $httpStatus);

        curl_close($curl);
        return $output;

    }


}
