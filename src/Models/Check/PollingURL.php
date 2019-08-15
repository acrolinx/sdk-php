<?php


namespace Acrolinx\SDK\Models\Check;


class PollingURL
{
    private $url;
    public function __construct(string $pollingUrl)
    {
        $this->url = $pollingUrl;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }



}
