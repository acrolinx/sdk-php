<?php


namespace Acrolinx\SDK\Utils;

use Acrolinx\SDK\AcrolinxEndpoint;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;


class Polling
{
    public function __construct()
    {

    }

    public function poll(AcrolinxEndpoint $endpoint, string $authToken,
                         LoopInterface $loop, string $url): PromiseInterface
    {

        $deferred = new Deferred();

        $loop->addPeriodicTimer(1, function (TimerInterface $timer)
        use ($deferred, $loop, &$endpoint, &$authToken, &$url) {

            $endpoint->pollforCheckResult($url, $authToken)->then(
                function (ResponseInterface $response) use ($deferred, $loop, &$timer) {
                    if ($response->getStatusCode() == 201) {
                        // ToDo: Notify progress
                        fwrite(STDERR, print_r(PHP_EOL . 'Progress status: ' . var_dump($response->getStatusCode()) . PHP_EOL));
                    }

                    if ($response->getStatusCode() == 200) {
                        $deferred->resolve($response);
                        $loop->cancelTimer($timer);
                    }

                }, function ($reason) use ($deferred, $loop, &$timer) {
                $deferred->reject($reason);
                $loop->cancelTimer($timer);
            });
        });

        return $deferred->promise();
    }

}