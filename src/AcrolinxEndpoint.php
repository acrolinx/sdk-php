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
use Acrolinx\SDK\Models\Check\PollingURL;
use Acrolinx\SDK\Models\ContentAnalysisDashboardLinks;
use Acrolinx\SDK\Models\CheckingCapabilities;
use Acrolinx\SDK\Models\CheckRequest;
use Acrolinx\SDK\Models\CheckResponse;
use Acrolinx\SDK\Models\CheckResult;
use Acrolinx\SDK\Models\PlatformCapabilities;
use Acrolinx\SDK\Models\PlatformInformation;
use Acrolinx\SDK\Models\PollingLink;
use Acrolinx\SDK\Models\Response\ProgressResponse;
use Acrolinx\SDK\Models\SignInSuccessData;
use Acrolinx\SDK\Models\SsoSignInOptions;
use Acrolinx\SDK\Utils\AcrolinxLogger;
use Monolog\Logger;
use React\Http\Browser;
use Exception;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;


class AcrolinxEndpoint
{
    private $clientLocale = 'en';
    private $props = null;
    private $client;
    private $loop;
    private $logger;

    /**
     * AcrolinxEndpoint constructor.
     * @param AcrolinxEndPointProperties $props
     * @param LoopInterface $loop
     * @param $logger
     */
    public function __construct(AcrolinxEndPointProperties $props, LoopInterface $loop)
    {
        $this->props = $props;
        $this->client = new Browser($loop);
        $this->loop = $loop;
        $this->logger = AcrolinxLogger::getInstance('./logs/acrolinx.log', Logger::INFO);
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
     * Get Acrolinx Platform information
     *
     * @return PromiseInterface containing {@see \Acrolinx\SDK\Models\PlatformInformation} or Exception
     */
    public function getPlatformInformation(): PromiseInterface
    {
        $deferred = new Deferred();

        $this->client->get($this->props->platformUrl . '/api/v1/', $this->getCommonHeaders(null))->then(function (ResponseInterface $response) use ($deferred) {
            $result = json_decode($response->getBody()->getContents(), true);
            $object = json_decode(json_encode($result['data']), FALSE);
            $platformInformation = new PlatformInformation($object);
            $deferred->resolve($platformInformation);
        }, function (Exception $reason) use ($deferred) {
            $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(),
                $reason->getPrevious(), 'Fetching platform information failed');
            $deferred->reject($exception);
        });

        return $deferred->promise();
    }

    private function getCommonHeaders($authToken)
    {
        $headers = [
            'X-Acrolinx-Client' => $this->props->clientSignature . ';' . $this->props->clientVersion,
            'X-Acrolinx-Base-Url' => $this->props->platformUrl,
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
     * Sign in to authenticate with the Acrolinx Core Platform.
     *
     * @param SsoSignInOptions $options
     * @return PromiseInterface containing {@see \Acrolinx\SDK\Models\SignInSuccessData} or Exception
     */
    public function signIn(SsoSignInOptions $options): PromiseInterface
    {
        $deferred = new Deferred();

        $headers = array_merge($this->getSsoRequestHeaders($options), $this->getCommonHeaders(null));
        $this->client->post($this->props->platformUrl . '/api/v1/auth/sign-ins', $headers)->then(function (ResponseInterface $response)
        use ($deferred) {

            if ($response->getStatusCode() == 200) {
                $successResponse = new SignInSuccessData($response);
                $deferred->resolve($successResponse);
            } else {
                $exception = new AcrolinxServerException('Single Sign On failed', $response->getStatusCode(),
                    null, 'SignIn Failed');
                $deferred->reject($exception);
            }


        }, function (Exception $reason) use ($deferred) {
            $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(),
                $reason->getPrevious(), 'SignIn Failed');
            $deferred->reject($exception);

        });

        return $deferred->promise();

    }

    private function getSsoRequestHeaders(SsoSignInOptions $options): array
    {
        return [
            $options->getUsernameKey() => $options->getUserId(),
            $options->getPasswordKey() => $options->getPassword()
        ];
    }

    /**
     * Get platform capabilities
     *
     * @param string $authToken
     * @return PromiseInterface containing {@see \Acrolinx\SDK\Models\PlatformCapabilities} or Exception
     */
    public function getCapabilities(string $authToken): PromiseInterface
    {
        $deferred = new Deferred();

        $this->client->get($this->props->platformUrl . '/api/v1/capabilities',
            $this->getCommonHeaders($authToken))->then(function (ResponseInterface $response) use ($deferred) {
            $platformCapabilities = new PlatformCapabilities($response);
            $deferred->resolve($platformCapabilities);
        }, function (Exception $reason) use ($deferred) {
            $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(),
                $reason->getPrevious(), 'Fetching Platform capabilities failed');
            $deferred->reject($exception);
        });

        return $deferred->promise();
    }

    /**
     * Submit a check.
     *
     * @param string $authToken
     * @param CheckRequest $request
     * @return PromiseInterface containing {@see \Acrolinx\SDK\Models\CheckResponse} or Exception
     */
    public function check(string $authToken, CheckRequest $request, $attempt = 1): PromiseInterface
    {
        $deferred = new Deferred();

        $this->client->post($this->props->platformUrl . '/api/v1/checking/checks',
            $this->getCommonHeaders($authToken), $request->getJson())->then(function (ResponseInterface $response)
        use ($request, $authToken, $attempt, $deferred) {
            $checkResponse = new CheckResponse($response);
            $deferred->resolve($checkResponse);
        }, function (Exception $reason) use ($request, $authToken, $attempt, $deferred) {
            $responseCode = $reason->getCode();
            $retryAfterExists = $this->getRetryAfter($reason->getResponse());

            if ($responseCode == 429 && $attempt <= 5 && $retryAfterExists) {
                $this->logger->info('Check failed with 429. Retrying attempt # ' . $attempt);
                $retryAfter = $retryAfterExists[0] * 1000;
                $retryInterval = $retryAfter * pow(2, $attempt);
                $this->loop->addTimer($retryInterval, function () use ($authToken, $request, $attempt, $deferred) {
                    $this->check($authToken, $request, ++$attempt)->then(function (CheckResponse $checkResponse) use ($deferred) {
                        $deferred->resolve($checkResponse);
                    }, function (Exception $reason) use ($deferred) {
                        $deferred->reject($reason);
                    });
                });
            } else {
                $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(),
                    $reason->getPrevious(), 'Submitting check failed');
                $deferred->reject($exception);
            }
        });

        return $deferred->promise();
    }

    /**
     * Get Retry-After Header.
     *
     * @param ResponseInterface $response
     * @return array|null
     */
    private function getRetryAfter(ResponseInterface $response): ?array
    {
        $retryAfter = $response->getHeaders()['Retry-After'];
        if (empty($retryAfter)) {
            $retryAfter = $response->getHeaders()['retry-after'];
        }
        if (empty($retryAfter)) {
            $retryAfter = $response->getHeaders()['RETRY-AFTER'];
        }
        return $retryAfter;
    }

    /**
     * Get supported options for check.
     *
     * @param string $authToken
     * @return PromiseInterface containing {@see \Acrolinx\SDK\Models\CheckCapabilities} or Exception
     */
    public function getCheckingCapabilities(string $authToken): PromiseInterface
    {
        $deferred = new Deferred();
        $this->client->get($this->props->platformUrl . '/api/v1/checking/capabilities',
            $this->getCommonHeaders($authToken))->then(function (ResponseInterface $response) use ($deferred) {
            $responseBody = json_decode($response->getBody());
            $capabilities = new CheckingCapabilities($responseBody->data);
            $deferred->resolve($capabilities);
        }, function (Exception $reason) use ($deferred) {
            $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(),
                $reason->getPrevious(), 'Fetching checking capabilities failed');
            $deferred->reject($exception);
        });

        return $deferred->promise();
    }

    /**
     * Poll for a check result.
     *
     * @param string $url
     * @param string $authToken
     * @return PromiseInterface containg {@see \Acrolinx\SDK\Models\CheckResult} or Exception
     */
    public function pollForCheckResult(PollingURL $url, string $authToken): PromiseInterface
    {
        $deferred = new Deferred();
        $pollingLoop = $this->loop;


        $pollingLoop->addPeriodicTimer(1, function (TimerInterface $timer)
        use ($deferred, &$pollingLoop, &$authToken, &$url) {
            $this->client->get($url->getUrl(), $this->getCommonHeaders($authToken))->then(
                function (ResponseInterface $response) use ($deferred, &$pollingLoop, &$timer) {

                    if ($response->getStatusCode() == 202 || $response->getStatusCode() == 201) {
                        $progressResponse = new ProgressResponse($response);
                        sleep($progressResponse->getRetryAfter());

                    }
                    if ($response->getStatusCode() == 200) {
                        $checkResult = new CheckResult($response);
                        $deferred->resolve($checkResult);
                        $pollingLoop->cancelTimer($timer);
                    }
                }, function (Exception $reason) use ($deferred, &$pollingLoop, &$timer) {
                $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(), $reason->getPrevious(),
                    'Unable to fetch check result');
                $deferred->reject($exception);
                $pollingLoop->cancelTimer($timer);

            });
        });

        return $deferred->promise();
    }

    /**
     * Get the link to the Acrolinx Content Analysis Dashboard for a batch check.
     *
     * @param string $authToken
     * @param string $batchId
     * @return PromiseInterface containing {@see \Acrolinx\SDK\Models\ContentAnalysisDashboardLinks} or Exception
     */
    public function getAcrolinxContentAnalysisDashboard(string $authToken, string $batchId)
    {
        $deferred = new Deferred();

        $this->client->get($this->props->platformUrl . '/api/v1/checking/' . $batchId . '/contentanalysis',
            $this->getCommonHeaders($authToken))->then(function (ResponseInterface $response) use ($deferred) {

            $contentAnalysisDashboardLinks = new ContentAnalysisDashboardLinks($response);
            $deferred->resolve($contentAnalysisDashboardLinks);

        }, function (Exception $reason) use ($deferred) {
            $exception = new AcrolinxServerException($reason->getMessage(), $reason->getCode(), $reason->getPrevious(),
                'Unable to fetch Content Analysis Dashboard URL');
            $deferred->reject($exception);
        });

        return $deferred->promise();

    }
}
