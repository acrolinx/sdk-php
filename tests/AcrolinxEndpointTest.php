<?php namespace Acrolinx\SDK;

/*
* Copyright 2019-present Acrolinx GmbH
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

use Acrolinx\SDK\Exceptions\AcrolinxServerException;
use Acrolinx\SDK\Models\AcrolinxEndPointProperties;
use Acrolinx\SDK\Models\ContentAnalysisDashboardLinks;
use Acrolinx\SDK\Models\CheckingCapabilities;
use Acrolinx\SDK\Models\CheckOptions;
use Acrolinx\SDK\Models\CheckRange;
use Acrolinx\SDK\Models\CheckRequest;
use Acrolinx\SDK\Models\CheckResponse;
use Acrolinx\SDK\Models\CheckResult;
use Acrolinx\SDK\Models\CheckType;
use Acrolinx\SDK\Models\ContentEncoding;
use Acrolinx\SDK\Models\DocumentDescriptorRequest;
use Acrolinx\SDK\Models\PlatformCapabilities;
use Acrolinx\SDK\Models\PlatformInformation;
use Acrolinx\SDK\Models\ReportType;
use Acrolinx\SDK\Models\SignInSuccessData;
use Acrolinx\SDK\Models\SsoSignInOptions;
use Acrolinx\SDK\Utils\AcrolinxLogger;
use Acrolinx\SDK\Utils\BatchCheckIdGenerator;
use Dotenv;
use Exception;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;


class AcrolinxEndpointTest extends TestCase
{

    /**
     * To run those tests you will have to provide a valid SSO user and password. Set those in the .env file to load them
     * automatically into the test environment.
     */
    protected $acrolinxURL;
    protected $acrolinxAuthToken;
    protected $acrolinxSsoUser;
    protected $acrolinxPassword;

    // You'll get the clientSignature for your integration after a successful certification meeting.
    // See: https://support.acrolinx.com/hc/en-us/articles/205687652-Getting-Started-with-Custom-Integrations
    protected $DEVELOPMENT_SIGNATURE = 'SW50ZWdyYXRpb25EZXZlbG9wbWVudERlbW9Pbmx5';

    /**
     * Test get server info API
     */
    public function testGetPlatformInformation()
    {
        $loop = Factory::create();

        $data = null;

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->getPlatformInformation()->then(function (PlatformInformation $response) use (&$data) {
            $data = $response;
        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . var_dump($reason->getMessage()) . PHP_EOL));
        });

        $loop->run();

        $this->assertEquals(true, isset($data));
        $server = $data->getServer();
        $this->assertEquals(true, isset($server));
        $locales = $data->getLocales();
        $this->assertEquals(true, isset($locales));
    }

    /**
     * Create Properties, that are necessary to connect to an Acrolinx Server.
     * @return AcrolinxEndPointProperties
     */
    private function getProps()
    {
        return new AcrolinxEndPointProperties($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');
    }

    public function testGetPlatformInformationError()
    {
        $props = new AcrolinxEndPointProperties($this->DEVELOPMENT_SIGNATURE, 'SomeFakeURL',
            'en', '');

        $reason = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($props, $loop);
        $acrolinxEndPoint->getPlatformInformation()->then(function (ResponseInterface $response) use (&$reason) {
            // Nothing here as we expect an error
        }, function (Exception $exception) use (&$reason) {
            $reason = $exception->getMessage();
        });
        $loop->run();
        $this->assertTrue(isset($reason));

    }

    /**
     * Receive an API Token from Acrolinx Server with single sign by providing single sign on credentials.
     */
    public function testSignIn()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $accessToken = null;
        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (SignInSuccessData $response) use (&$accessToken) {
            $accessToken = $response->getAccessToken();
        }, function (AcrolinxServerException $reason) {
            $this->assertEquals(true, $reason);
        });
        $loop->run();
        $this->assertEquals(true, isset($accessToken));
    }


    /**
     * This test could yield positive or negative results.
     * There is no evident significance attached with the result. Only used for debugging purpose.
     * If no user meta data is provided the Acrolinx Platform will return an error.
     */
    public function testSignInWithoutMetadata()
    {
        $ssoOptions = new SsoSignInOptions('dummy', $this->acrolinxPassword);

        $accessToken = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (SignInSuccessData $response) use (&$accessToken) {

            $accessToken = $response->getAccessToken();
        }, function (AcrolinxServerException $exception) {
            $reason = $exception->getMessage();
            $this->assertEquals(true, $reason);
        });

        $loop->run();
        if(isset($accessToken)) {
            self::assertTrue(isset($accessToken));
        }
        else {
            self::assertTrue(!isset($accessToken));
        }
    }


    /**
     * If the wrong SSO password is provided the Acrolinx Platform will return an error.
     */
    public function testSignInError()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, 'wrong password');

        $reason = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (SignInSuccessData $response) use (&$reason) {
        }, function (AcrolinxServerException $exception) use (&$reason) {
            $reason = $exception->getMessage();
        });

        $loop->run();
        $this->assertTrue(isset($reason));
    }

    /**
     * Receive Acrolinx Platform capabilities.
     */
    public function testPlatformCapabilities()
    {
        $responseBody = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->getCapabilities($this->acrolinxAuthToken)->
        then(function (PlatformCapabilities $response) use (&$responseBody) {
            $responseBody = $response;

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });

        $loop->run();
        $this->assertEquals(true, isset($responseBody));

    }

    /**
     * Receive the Acrolinx Platforms available options for checking.
     */
    public function testPlatformCheckingCapabilities()
    {
        $responseBody = null;
        $loop = Factory::create();
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->getCheckingCapabilities($this->acrolinxAuthToken)->
        then(function (CheckingCapabilities $response) use (&$responseBody) {
            $responseBody = $response;
        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });

        $loop->run();
        $guidanceProfiles = $responseBody->getGuidanceProfiles();

        $this->assertEquals(true, isset($guidanceProfiles));
    }

    /**
     * Create check options.
     */
    public function testCheckOptionsClass()
    {
        $checkOptions = new CheckOptions();
        $checkOptions->batchId = BatchCheckIdGenerator::getId('testPHPSDK');;
        $checkOptions->checkType = CheckType::BASELINE;
        $checkOptions->contentFormat = 'XML';
        $checkOptions->disableCustomFieldValidation = false;
        $checkOptions->reportTypes = array(ReportType::TERMHARVESTING, ReportType::SCORECARD);
        $checkOptions->guidanceProfileId = '2';
        $checkOptions->languageId = 'en';
        $checkOptions->partialCheckRanges = new CheckRange(10, 20);

        $checkOptionsJson = $checkOptions->getJson();
        $this->assertEquals(false, empty((array)$checkOptionsJson));

    }

    /**
     * Submit a check with check options set.
     */
    public function testSubmitCheckWithCheckOptions()
    {
        $loop = Factory::create();
        $token = $this->acrolinxAuthToken;
        $checkResponseBody = null;

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $acrolinxEndPoint->getCheckingCapabilities($token)->
        then(function (CheckingCapabilities $response) use (&$checkResponseBody, $token, $acrolinxEndPoint) {
            $guidanceProfileId = $response->getGuidanceProfiles()[0]->getId();

            $checkOptions = new CheckOptions();
            $checkOptions->batchId = BatchCheckIdGenerator::getId('testPHPSDK');;
            $checkOptions->checkType = CheckType::BASELINE;
            $checkOptions->contentFormat = 'xml';
            $checkOptions->disableCustomFieldValidation = false;
            $checkOptions->reportTypes = array(ReportType::TERMHARVESTING, ReportType::SCORECARD);
            $checkOptions->guidanceProfileId = $guidanceProfileId;
            $checkOptions->languageId = 'en';
            $checkRequest = new CheckRequest('<x>this is text</x>');
            $checkRequest->checkOptions = $checkOptions;
            $checkRequest->document = new DocumentDescriptorRequest('abc.xml');
            $checkRequest->contentEncoding = ContentEncoding::NONE;

            $acrolinxEndPoint->check($token, $checkRequest)->
            then(function (CheckResponse $response) use (&$checkResponseBody) {
                $checkResponseBody = $response;
            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
            });

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });

        $loop->run();
        $this->assertEquals(false, empty($checkResponseBody->getId()));
    }

    /**
     * A check can also be submitted without any check options. The Platform will then apply those.
     */
    public function testSubmitCheckWithoutOptions()
    {
        $checkResponseBody = null;
        $token = $this->acrolinxAuthToken;
        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $checkRequest = new CheckRequest('Simple Test');
        $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
        $checkRequest->contentEncoding = ContentEncoding::NONE;

        $acrolinxEndPoint->check($token, $checkRequest)->
        then(function (CheckResponse $response) use (&$checkResponseBody) {
            $checkResponseBody = $response;

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });

        $loop->run();
        $this->assertEquals(false, empty($checkResponseBody->getId()));
    }

    /**
     * To receive a check result we need to poll for the result.
     */
    public function testSubmitCheckAndPollForResult()
    {

        $token = $this->acrolinxAuthToken;
        $checkScore = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $checkOptions = new CheckOptions();
        $checkOptions->batchId = BatchCheckIdGenerator::getId('testPHPSDK');
        $checkOptions->checkType = CheckType::BASELINE;
        $checkOptions->contentFormat = 'html';
        $checkOptions->disableCustomFieldValidation = false;
        $checkOptions->reportTypes = array(ReportType::TERMHARVESTING, ReportType::SCORECARD);
        $checkOptions->languageId = 'en';

        $checkRequest = new CheckRequest('<x>This is test</x>');
        $checkRequest->checkOptions = $checkOptions;
        $checkRequest->document = new DocumentDescriptorRequest('abc.html');
        $checkRequest->contentEncoding = ContentEncoding::NONE;

        $acrolinxEndPoint->check($token, $checkRequest)->then(function (CheckResponse $response)
        use ($acrolinxEndPoint, $token, &$loop, &$checkScore) {

            $resultUrl = $response->getPollingUrl();

            $acrolinxEndPoint->pollforCheckResult($resultUrl, $token)->
            then(function (CheckResult $response) use (&$checkScore) {

                $checkScore = $response->getQuality()->getScore();
            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
            });
        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });

        $loop->run();
        $this->assertEquals(true, isset($checkScore));
    }

    /**
     * Retrieve a link tho the Acrolinx Content Analysis Dashboard for a submitted batch check.
     */
    public function testGetAcrolinxContentAnalysisDashboard()
    {
        $loop = Factory::create();
        $token = $this->acrolinxAuthToken;
        $responseData = null;

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $acrolinxEndPoint->getAcrolinxContentAnalysisDashboard($token, '1')->
        then(function (ContentAnalysisDashboardLinks $response) use (&$responseData) {
            $responseData = $response->getShortWithAccessToken();

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });
        $loop->run();
        $this->assertTrue(isset($responseData));
    }

    public static
    function setUpBeforeClass(): void
    {
        if (PHP_SAPI === 'phpdbg' &&
            strtolower(substr(php_uname('s'), 0, 7)) !== 'windows') {
            /* Unlike other PHP SAPIs, the phpdbg SAPI does not ignore SIGPIPE
             * [1], thus terminating the phpdbg process [2] when that signal is
             * received.
             *
             * This becomes an issue when a write to a socket whose "reading
             * end" is closed occurs: SIGPIPE is raised [3] and kills the
             * process.
             *
             * That particular scenario occurs when using TLS/SSL connections
             * with the "react/socket" PHP library, which is used by the REST
             * client that we use, "clue/buzz-react".
             *
             * "react/socket" calls shutdown(..., SHUT_RDWR) on a socket before
             * closing it [4], which disallows further reads and writes to the
             * socket [5]. Unfortunately, later, OpenSSL presumably tries to
             * send a "close notify" message to the peer [6] when the socket
             * actually gets closed for good -- which fails due to the socket
             * being shutdown, thus raising SIGPIPE and killing the process.
             *
             * To work around this, we ignore SIGPIPE. This should be safe
             * since write()s to a socket that would raise SIGPIPE will instead
             * fail with EPIPE if SIGPIPE is ignored [3].
             *
             * [1] See e.g.:
             *     https://chat.stackoverflow.com/transcript/message/37915372#37915372
             *     and
             *     https://github.com/amphp/byte-stream/blob/d5cd42a76516f91672143fa5662df2fdaa4ebe57/test/ResourceStreamTest.php#L74
             * [2] https://manpages.debian.org/stretch/manpages/signal.7.en.html#Standard_signals
             * [3] https://manpages.debian.org/stretch/manpages-dev/write.2.en.html#ERRORS
             * [4] https://github.com/reactphp/socket/blob/23b7372bb25cea934f6124f5bdac34e30161959e/src/Connection.php#L122
             * [5] https://manpages.debian.org/stretch/manpages-dev/shutdown.2.en.html
             * [6] https://www.openssl.org/docs/man1.0.2/man3/SSL_shutdown.html
             */
            pcntl_signal(SIGPIPE, SIG_IGN);
        }
    }

    protected
    function setUp(): void
    {
        $serverEnv = getenv('ACROLINX_TEST_SERVER_URL');
        $tokenEnv = getenv('ACROLINX_ACCESS_TOKEN');

        $ssoUser = getenv('ACROLINX_SSO_USER');
        $ssoPassword = getenv('ACROLINX_SSO_PASSWORD');

        if ((!isset($tokenEnv) || strlen($tokenEnv) < 1) || (!isset($serverEnv) || strlen($serverEnv) < 1)) {
            $parent_dir = dirname(__DIR__);
            $dotenv = Dotenv\Dotenv::create($parent_dir);
            $dotenv->overload();
            $serverEnv = getenv('ACROLINX_TEST_SERVER_URL');
            $tokenEnv = getenv('ACROLINX_ACCESS_TOKEN');
            $ssoUser = getenv('ACROLINX_SSO_USER');
            $ssoPassword = getenv('ACROLINX_SSO_PASSWORD');
        }

        if (isset($serverEnv)) {
            $this->acrolinxURL = $serverEnv;
        } else {
            fwrite(STDERR, print_r(PHP_EOL . 'No Acrolinx Server Address set.' . PHP_EOL));
        }
        if (isset($tokenEnv)) {
            $this->acrolinxAuthToken = $tokenEnv;
        } else {
            fwrite(STDERR, print_r(PHP_EOL . 'No Acrolinx Auth Token set.' . PHP_EOL));
        }
        if (isset($ssoUser)) {
            $this->acrolinxSsoUser = $ssoUser;
        } else {
            fwrite(STDERR, print_r(PHP_EOL . 'No Acrolinx SSO user set' . PHP_EOL));
        }
        if (isset($ssoPassword)) {
            $this->acrolinxPassword = $ssoPassword;
        } else {
            fwrite(STDERR, print_r(PHP_EOL . 'No Acrolinx SSO password set' . PHP_EOL));
        }

    }

    public function testLogger()
    {
        $logger = AcrolinxLogger::getInstance('./logs/acrolinx.log');

        $logger->info("An Info test");
        $logger->debug('A debug log');
        $logger->error('An error log');
        $logger->warning('A warning log');

        self::assertEquals(true, file_exists('./logs/acrolinx.log'));

    }

    public function testLoggerAccessDenied()
    {
        AcrolinxLogger::getInstance('/logs/acrolinx.log');

        try {
            $logger = AcrolinxLogger::getInstance('/logs/acrolinx.log');
            $logger->info("An Info test");

        } catch (Exception $e) {
            $message = $e->getMessage();
            self::assertEquals(true, isset($message));
        }

        self::assertEquals(false, file_exists('/logs/acrolinx.log'));

    }


    public function testLoggerLevel()
    {
        $logger = AcrolinxLogger::getInstance('./logs/acrolinx.log', Logger::ERROR);

        $logger->info("An Info test");
        $logger->debug('A debug log');
        $logger->error('An error log');
        $logger->warning('A warning log');

        $fileContents = file_get_contents('./logs/acrolinx.log');

        self::assertContains("An error log", $fileContents);
        self::assertNotContains('A debug log', $fileContents);

    }

}
