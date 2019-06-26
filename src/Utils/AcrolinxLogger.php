<?php


namespace Acrolinx\SDK\Utils;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AcrolinxLogger
{
    private static $logger;

    public static function getInstance($file, $level = Logger::INFO): Logger
    {
        if (!isset(self::$logger)) {
            self::$logger = new Logger('acrolinx-logger');
            self::$logger->pushHandler(new StreamHandler($file,Logger::INFO) );
        }
        return self::$logger;
    }

}
