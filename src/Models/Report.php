<?php


namespace Acrolinx\SDK\Models;


use http\Url;

class Report
{
    private $linkAuthenticated;
    private $link;

    public function __construct(Url $linkAuthenticated, Url $link)
    {
        $this->linkAuthenticated = $linkAuthenticated;
        $this->link = $link;
    }

    /**
     * @return Url
     */
    public function getLink(): Url
    {
        return $this->link;
    }

    /**
     * @return Url
     */
    public function getLinkAuthenticated(): Url
    {
        return $this->linkAuthenticated;
    }
}