<?php


namespace Acrolinx\SDK\Models\Response;


use Psr\Http\Message\ResponseInterface;

class ProgressResponse
{
    private $retryAfter;
    private $percent;
    private $message;

    public function __construct(ResponseInterface $response)
    {
        $responseBody = json_decode($response->getBody());

        $this->retryAfter = $responseBody->progress->retryAfter;
        $this->percent = $responseBody->progress->percent;
        $this->message = $responseBody->progress->message;

    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @return int
     */
    public function getRetryAfter()
    {
        return $this->retryAfter;
    }
}
