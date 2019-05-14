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

use Acrolinx\SDK\Exceptions\AcrolinxServerException;
use Acrolinx\SDK\Models\AcrolinxEndPointProperties;
use Acrolinx\SDK\Models\CheckRequest;
use Acrolinx\SDK\Models\CheckResponse;
use Acrolinx\SDK\Models\SsoSignInOptions;
use Clue\React\Buzz\Browser;
use Exception;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;


class AcrolinxEndpoint
{
    /**  @var string $serverAddress set the Platform URL to talk to */
    private $serverAddress = '';
    private $clientLocale = 'en';
    private $props = null;
    private $client;

    /**
     * AcrolinxEndpoint constructor.
     * @param AcrolinxEndPointProperties $props
     * @param LoopInterface $loop
     */
    public function __construct(AcrolinxEndPointProperties $props, LoopInterface $loop)
    {
        $this->props = $props;
        $this->client = new Browser($loop);
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
    public function getServerInfo(): PromiseInterface
    {
        return $this->client->get($this->props->serverAddress . '/api/v1/',
            $this->getCommonHeaders(null));
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

    /**
     * @param SsoSignInOptions $options
     * @return PromiseInterface
     */
    public function signIn(SsoSignInOptions $options): PromiseInterface
    {
        $headers = array_merge($this->getSsoRequestHeaders($options), $this->getCommonHeaders(null));
        return $this->client->post($this->props->serverAddress . '/api/v1/auth/sign-ins', $headers);

    }

    private function getSsoRequestHeaders(SsoSignInOptions $options): array
    {
        return [
            $options->getUsernameKey() => $options->getUserId(),
            $options->getPasswordKey() => $options->getPassword()
        ];
    }

    /**
     * @param string $authToken
     * @return PromiseInterface
     */
    public function getCapabilities(string $authToken): PromiseInterface
    {
        return $this->client->get($this->props->serverAddress . '/api/v1/capabilities',
            $this->getCommonHeaders($authToken));
    }

    /**
     * @param string $authToken
     * @param CheckRequest $request
     * @return PromiseInterface
     */
    public function check(string $authToken, CheckRequest $request): PromiseInterface
    {
        $deferred = new Deferred();

        $this->client->post($this->props->serverAddress . '/api/v1/checking/checks',
            $this->getCommonHeaders($authToken), $request->getJson())->then(function (ResponseInterface $response)
        use ($deferred) {
            $responseBody = json_decode($response->getBody());
            $checkResponse = new CheckResponse($responseBody->data, $responseBody->links);
            $deferred->resolve($checkResponse);
        }, function (Exception $reason) use ($deferred) {
            $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(),
                $reason->getPrevious(), 'Submitting check failed');
            $deferred->reject($exception);

        });

        return $deferred->promise();
    }

    /**
     * @param string $authToken
     * @return PromiseInterface
     */
    public function getCheckingCapabilities(string $authToken): PromiseInterface
    {
        return $this->client->get($this->props->serverAddress . '/api/v1/checking/capabilities',
            $this->getCommonHeaders($authToken));
    }

    public function pollforCheckResult(string $url, string $authToken): PromiseInterface
    {
        return $this->client->get($url, $this->getCommonHeaders($authToken));
    }

    public function getAcrolinxContentAnalysisDashboard(string $authToken, string $batchId)
    {
        return $this->client->get($this->props->serverAddress . '/api/v1/checking/' . $batchId . '/contentanalysis',
            $this->getCommonHeaders($authToken));
    }
}
