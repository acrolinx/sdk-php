<?php


namespace Acrolinx\SDK\Utils;


use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AcrolinxLogger
{
    private static $instance;
    private $logger;

    private function __construct($directory)
    {
        $this->logger = new Logger('acrolinx-logger');
        $this->logger->pushHandler(new StreamHandler($directory . '/logs/acrolinx.log'), Logger::INFO);
    }

    public static function getInstance($directory): AcrolinxLogger
    {
        $dir = rtrim($directory, '/');
        if (!file_exists($dir)) {
            throw new Exception('Inavalid directory');
        }

        if (!isset(self::$instance)) {
            self::$instance = new AcrolinxLogger($dir);
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
