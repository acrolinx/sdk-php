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
     * @throws AcrolinxServerException
     */
    public function getServerInfo()
    {
        try {
            return $this->getData('/api/v1/', null, null);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    /**
     * @param SsoSignInOptions $options
     * @return array
     *
     * @throws AcrolinxServerException
     */
    public function signIn(SsoSignInOptions $options)
    {
        try {
            return $this->postData('/api/v1/auth/sign-ins', null, null, $options);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    /**
     * Get an authentication token for single sign on username and password.
     * @param string $username
     * @param string $password
     * @return string AuthToken
     * @throws AcrolinxServerException
     */
    public function getAuthTokenFromSSOCredentials(string $username, string $password): string
    {
        $ssoOptions = new SsoSignInOptions($username, $password);
        try {
            $result = $this->signIn($ssoOptions);
            $response = $result['response'];
            $responseJSON = json_decode($response, true);
            return $responseJSON['data']['accessToken'];
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    /**
     * @param $authToken
     * @return array
     * Get platform capabilities
     * @throws AcrolinxServerException
     */
    public function getCapabilities(string $authToken)
    {
        try {
            return $this->getData('/api/v1/capabilities', null, $authToken);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    /**
     * @param string $authToken
     * @return array
     * @throws AcrolinxServerException
     */
    public function getCheckingCapabilities(string $authToken)
    {
        try {
            return $this->getData('/api/v1/checking/capabilities', null, $authToken);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    private function getCommonHeaders($authToken)
    {
        $headers = array(
            'X-Acrolinx-Client: ' . $this->props->clientSignature,
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

    private function getSsoRequestHeaders(SsoSignInOptions $options): array
    {
        return array(
            $options->getUsernameKey() . ': ' . $options->getUserId(),
            $options->getPasswordKey() . ': ' . $options->getPassword()
        );

    }

    private function postData(string $path, $authToken, $data, SsoSignInOptions $options)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        try {
            return $this->curlSetup($curl, $path, $authToken, $options);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    private function getData(string $path, $data, $authToken)
    {
        $curl = curl_init();


        if ($data) {
            $path = sprintf("%s?%s", $path, http_build_query($data));
        }

        try {
            return $this->curlSetup($curl, $path, $authToken, null);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }
    }

    private function putData(string $path, string $authToken, $data)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        try {
            return $this->curlSetup($curl, $path, $authToken, null);
        } catch (AcrolinxServerException $e) {
            throw $e;
        }

    }

    /**
     * @param $curl
     * @param string $path
     * @param $authToken
     * @param $options
     * @return array
     * @throws AcrolinxServerException
     */
    private function curlSetup($curl, string $path, $authToken, $options)
    {
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

        if ($result === false) {
            $error = 'Curl-Fehler: ' . curl_error($curl);
            curl_close($curl);
            throw new AcrolinxServerException($error);
        }

        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $output = array("response" => $result, "status" => $httpStatus);

        curl_close($curl);

        $responseJSON = json_decode($result, true);

        if (array_key_exists('error', $responseJSON)) {
            $error = $responseJSON['error'];

            if (isset($error) && isset($error['title'])) {
                throw new AcrolinxServerException(
                    $error['title'] . ': ' . $error['detail'], 0, NULL, $error['status']);
            }
        }
        return $output;
    }
}
