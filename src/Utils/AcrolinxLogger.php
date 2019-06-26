<?php


namespace Acrolinx\SDK\Utils;


use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

class AcrolinxLogger
{
    private static $instance;
    private $logger;

    private function __construct($file, $level = Logger::INFO)
    {

        $this->logger = new Logger('acrolinx-logger');
        $this->logger->pushProcessor(new IntrospectionProcessor($level));
        $this->logger->pushHandler(new StreamHandler($file), $level);
    }

    public static function getInstance($file): AcrolinxLogger
    {
        if (!isset(self::$instance)) {

            self::$instance = new AcrolinxLogger($file);
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
        $this->logger->debug($msg);
    }


    /**
     * @param string $msg
     */
    public function logWarning(string $msg): void
    {
        $this->logger->warning($msg);
    }


    /**
     * @param string $msg
     */
    public function logError(string $msg): void
    {
        $this->logger->error($msg);

    }
}
