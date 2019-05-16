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
use Acrolinx\SDK\Models\ReportType;
use Acrolinx\SDK\Models\SignInSuccessData;
use Acrolinx\SDK\Models\SsoSignInOptions;
use Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;


class AcrolinxEndpointTest extends TestCase
{

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
    public function testGetServerInfo()
    {
        $loop = Factory::create();

        $data = null;
        $status = null;

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->getServerInfo()->then(function (ResponseInterface $response) use (&$data, &$status) {
            $result = json_decode($response->getBody()->getContents(), true);
            $data = $result['data'];
            $status = $response->getStatusCode();
            // fwrite(STDERR, print_r(PHP_EOL . var_dump($response->getStatusCode()) . PHP_EOL));


        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . var_dump($reason->getMessage()) . PHP_EOL));
            // $this->assertEquals(true, $reason);
        });

        $loop->run();

        $this->assertEquals(true, isset($data));
        $this->assertEquals(200, $status);

    }

    private function getProps()
    {
        return new AcrolinxEndPointProperties($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');
    }

    public function testGetServerInfoError()
    {
        $props = new AcrolinxEndPointProperties($this->DEVELOPMENT_SIGNATURE, 'SomeFakeURL',
            'en', '');

        $reason = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($props, $loop);
        $acrolinxEndPoint->getServerInfo()->then(function (ResponseInterface $response) use (&$reason) {
            // Nothing here as we expect an error
        }, function (Exception $exception) use (&$reason) {
            $reason = $exception->getMessage();
            // fwrite(STDERR, print_r(PHP_EOL . var_dump($reason->getMessage()) . PHP_EOL));
        });
        $loop->run();
        $this->assertTrue(isset($reason));

    }

    public function testSignIn()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $accessToken = null;
        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (SignInSuccessData $response) use (&$accessToken) {
            $accessToken = $response->getAccessToken();
        }, function (AcrolinxServerException $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });
        $loop->run();
        $this->assertEquals(true, isset($accessToken));
    }

    public function testSignInError()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, 'wrong password');

        $reason = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (SignInSuccessData $response) use (&$reason) {
            // not needed as we expect an error
        }, function (AcrolinxServerException $exception) use (&$reason) {
            // fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
            //    ' | StatusCode: ' . PHP_EOL));
            $reason = $exception->getMessage();
        });

        $loop->run();
        $this->assertTrue(isset($reason));
    }

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
        //fwrite(STDERR, print_r($responseBody));
        $this->assertEquals(true, isset($responseBody));

    }

    public function testPlatformCheckingCapabilities()
    {
        $responseBody = null;
        $loop = Factory::create();
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->getCheckingCapabilities($this->acrolinxAuthToken)->
        then(function (CheckingCapabilities $response) use (&$responseBody) {
            $responseBody = $response;
            // fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
            //    ' | StatusCode: ' . PHP_EOL));
        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });

        $loop->run();
        $guidanceProfiles = $responseBody->getGuidanceProfiles();

        $this->assertEquals(true, isset($guidanceProfiles));
    }

    public
    function testCheckOptionsClass()
    {
        $checkOptions = new CheckOptions();
        $checkOptions->batchId = 1;
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

    public
    function testSubmitCheckWithCheckOptions()
    {
        $loop = Factory::create();
        $token = $this->acrolinxAuthToken;
        $checkResponseBody = null;

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $acrolinxEndPoint->getCheckingCapabilities($token)->
        then(function (CheckingCapabilities $response) use (&$checkResponseBody, $token, $acrolinxEndPoint) {
            $guidanceProfileId = $response->getGuidanceProfiles()[0]->getId();

            // fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
            //    ' | StatusCode: ' . PHP_EOL));

            $checkOptions = new CheckOptions();
            $checkOptions->batchId = 1;
            $checkOptions->checkType = CheckType::BASELINE;
            $checkOptions->contentFormat = 'TEXT';
            $checkOptions->disableCustomFieldValidation = false;
            $checkOptions->reportTypes = array(ReportType::TERMHARVESTING, ReportType::SCORECARD);
            $checkOptions->guidanceProfileId = $guidanceProfileId;
            $checkOptions->languageId = 'en';
            $checkRequest = new CheckRequest('Simple Test');
            $checkRequest->checkOptions = $checkOptions;
            $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
            $checkRequest->contentEncoding = ContentEncoding::NONE;

            //fwrite(STDERR, print_r(PHP_EOL . 'Content::: '.$checkRequest->content .
              //  ' | StatusCode: ' . PHP_EOL));

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

    public
    function testSubmitCheckWithoutOptions()
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

    public
    function testSubmitCheckAndPollForResult()
    {

        $token = $this->acrolinxAuthToken;
        $checkScore = null;

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $checkRequest = new CheckRequest('Verrry wrooong sentenceee');
        $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
        $checkRequest->contentEncoding = ContentEncoding::NONE;

        $acrolinxEndPoint->check($token, $checkRequest)->then(function (CheckResponse $response)
        use ($acrolinxEndPoint, $token, &$loop, &$checkScore) {

            $resultUrl = $response->getLinks()['result'];

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

    public function testGetAcrolinxContentAnalysisDashboard()
    {
        $loop = Factory::create();
        $token = $this->acrolinxAuthToken;
        $responseData = null;

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);

        $acrolinxEndPoint->getAcrolinxContentAnalysisDashboard($token, '1')->
        then(function (ResponseInterface $response) use (&$responseData) {
            $responseBody = $response->getBody();
            $checkResult = json_decode($responseBody);
            $responseData = $checkResult->data;
        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
        });
        $loop->run();
        $this->assertTrue(isset($responseData->links));
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
}
