<?php


namespace Acrolinx\SDK\Utils;

use Acrolinx\SDK\AcrolinxEndpoint;
use GuzzleHttp\Exception\RequestException;


class Polling
{
    private $pollingUrl;
    private $timeoutInSeconds;

    public function __construct(string $url,int $timeoutInSeconds)
    {
        $this->pollingUrl = $url;
        $this->timeoutInSeconds = $timeoutInSeconds;
    }

    public function poll(AcrolinxEndpoint $endpoint, $authToken)
    {
        $result = null;

        while(true)
        {
            try
            {
                $promise = $endpoint->pollforCheckResult($this->pollingUrl, $authToken)->wait(true);
                $response = $promise->getBody();

                if ($promise->getStatusCode() == 200)
                {
                    $result = $response;
                    break;
                }
                if ($promise->getStatusCode() == 201)
                {
                    $retryAfter = $response->progress->retryAfter;
                    sleep($retryAfter);
                    continue;
                }
            }
            catch (RequestException $e)
            {
                $result = $e;
                break;
            }

        }

        return $result;
    }

}