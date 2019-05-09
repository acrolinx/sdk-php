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

use Acrolinx\SDK\Models\SsoSignInOptions;
use Acrolinx\SDK\Models\AcrolinxEndPointProps;
use Acrolinx\SDK\Models\CheckRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;

class AcrolinxEndpoint
{
    /**  @var string $serverAddress set the Platform URL to talk to */
    private $serverAddress = '';
    private $clientLocale = 'en';
    private $props = null;
    private $client;

    /**
     * AcrolinxEndpoint constructor.
     * @param $props
     */
    public function __construct(AcrolinxEndPointProps $props)
    {
        $this->props = $props;
        $this->client = new Client(['base_uri' => $props->serverAddress]);
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
     * @throws RequestException
     */
    public function getServerInfo(): PromiseInterface
    {
        $request = new Request('GET', '/api/v1/',
            $this->getCommonHeaders(null), null);
        return $this->client->sendAsync($request);
    }

    /**
     * @param SsoSignInOptions $options
     * @return PromiseInterface
     * @throws RequestException
     */
    public function signIn(SsoSignInOptions $options): PromiseInterface
    {
        $headers = array_merge($this->getSsoRequestHeaders($options), $this->getCommonHeaders(null));
        $request = new Request('POST', '/api/v1/auth/sign-ins',
            $headers, null);
        return $this->client->sendAsync($request);
    }

    /**
     * @param string $authToken
     * @return PromiseInterface
     * @throws RequestException
     */
    public function getCapabilities(string $authToken): PromiseInterface
    {
        $request = new Request('GET', '/api/v1/capabilities',
            $this->getCommonHeaders($authToken), null);
        return $this->client->sendAsync($request);
    }

    /**
     * @param string $authToken
     * @param CheckRequest $request
     * @return PromiseInterface
     * @throws RequestException
     */
    public function check(string $authToken, CheckRequest $request): PromiseInterface
    {
        $request = new Request('POST', '/api/v1/checking/checks',
            $this->getCommonHeaders($authToken), $request->getJson());
        return $this->client->sendAsync($request);
    }

    /**
     * @param string $authToken
     * @return PromiseInterface
     * @throws RequestException
     */
    public function getCheckingCapabilities(string $authToken): PromiseInterface
    {
        $request = new Request('GET', '/api/v1/checking/capabilities',
            $this->getCommonHeaders($authToken), null);
        return $this->client->sendAsync($request);
    }

    private function getCommonHeaders($authToken)
    {
        $headers = [
            'X-Acrolinx-Client' => $this->props->clientSignature,
            'X-Acrolinx-Base-Url' => $this->props->baseUrl,
            'Content-Type' => 'application/json'
        ];

        if (!is_null($this->props->clientLocale)) {
            $headers['X-Acrolinx-Client-Locale'] = $this->props->clientLocale;
        }

        if (!is_null($authToken)) {
            $headers['X-Acrolinx-Auth'] = $authToken;
        }


        return $headers;
    }

    private function getSsoRequestHeaders(SsoSignInOptions $options): array
    {
        return [
            $options->getUsernameKey() => $options->getUserId(),
            $options->getPasswordKey() => $options->getPassword()
        ];
    }
}
