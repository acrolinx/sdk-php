<?php


namespace Acrolinx\SDK\Models;


class CheckResponse extends SuccessResponse
{
    public $links = array('result'=>'', 'cancel'=>'');

    public function __construct($data, array $links)
    {
        parent::__construct($data, $links);
        $this->links['result'] = $links['result'];
        $this->links['cancel'] = $links['cancel'];
    }

}