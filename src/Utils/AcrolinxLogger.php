<?php


namespace Acrolinx\SDK\Utils;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AcrolinxLogger
{
    private static $instance;
    private $logger;

    private function __construct()
    {
        $this->logger = new Logger('acrolinx-logger');
        //file_put_contents(__DIR__ . '/acrolinx.log', '', FILE_APPEND | LOCK_EX);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/logs/acrolinx.log'), Logger::INFO);
    }

    public static function getInstance(): AcrolinxLogger
    {
        if (!isset(self::$instance)) {
            self::$instance = new AcrolinxLogger();
        }
        return self::$instance;
    }

    /**
     * @param string $msg
     */
    public function logInfo(string $msg): void
    {
        $this->logger->info($msg);
    }

    /**
     * @param string $msg
     */
    public function logDebug(string $msg): void
    {
        $this->logger->info($msg);
    }


    /**
     * @param string $msg
     */
    public function logWarning(string $msg): void
    {
        $this->logger->info($msg);
    }


    /**
     * @param string $msg
     */
    public function logError(string $msg): void
    {
        $this->logger->info($msg);

    }
}
