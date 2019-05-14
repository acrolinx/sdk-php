<?php


namespace Acrolinx\SDK\Models;


class SuccessResponse
{
    public $data;
    public $links;

    public function __construct($data, $links)
    {
        $this->data = $data;
        $this->links = $links;
    }
}