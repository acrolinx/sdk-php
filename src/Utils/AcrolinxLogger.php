<?php


namespace Acrolinx\SDK\Utils;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

class AcrolinxLogger
{
    private static $logger;

    public static function getInstance($file, $level = Logger::INFO): Logger
    {
        if (!isset(self::$logger)) {
            self::$logger = new Logger('acrolinx-logger');
            self::$logger->pushHandler(new StreamHandler($file,$level) );
            self::$logger->pushProcessor(new IntrospectionProcessor($level));
        }
        return self::$logger;
    }

}
