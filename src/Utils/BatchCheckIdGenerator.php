<?php


namespace Acrolinx\SDK\Utils;

use Ramsey\Uuid\Uuid;

class BatchCheckIdGenerator
{
    public static function getId(String $integrationShortName): String
    {
        $uuid4 = Uuid::uuid4();
        echo $uuid4->toString();
        if ((!isset($integrationShortName) || trim($integrationShortName) === '')) {
            $name = 'phpSDK';
        } else {
            $name = str_replace(' ', '-', $integrationShortName);
        }
        return 'gen.' . $name . '.' . $uuid4;
    }
}
