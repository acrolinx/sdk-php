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

use Acrolinx\SDK\Models\AcrolinxEndPointProperties;
use Acrolinx\SDK\Models\CheckOptions;
use Acrolinx\SDK\Models\CheckRange;
use Acrolinx\SDK\Models\CheckRequest;
use Acrolinx\SDK\Models\CheckType;
use Acrolinx\SDK\Models\ContentEncoding;
use Acrolinx\SDK\Models\DocumentDescriptorRequest;
use Acrolinx\SDK\Models\ReportType;
use Acrolinx\SDK\Models\SsoSignInOptions;
use Acrolinx\SDK\Utils\Polling;
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


        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
         $acrolinxEndPoint->getServerInfo()->then(function (ResponseInterface $response) {
            $result = json_decode($response->getBody()->getContents(), true);
            $data = $result['data'];
            $this->assertEquals(true, isset($data));
            $this->assertEquals(200, $response->getStatusCode());
            fwrite(STDERR, print_r(PHP_EOL . var_dump($response->getStatusCode()) . PHP_EOL));


        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . var_dump($reason->getMessage()) . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();

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


        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($props, $loop);
        $acrolinxEndPoint->getServerInfo()->then(function (ResponseInterface $response) {
            $result = json_decode($response->getBody()->getContents(), true);
            $data = $result['data'];
            $this->assertEquals(true, isset($data));
            $status = $response->getStatusCode();
            fwrite(STDERR, print_r(PHP_EOL . var_dump($status) . PHP_EOL));
            $this->assertEquals(200, $status);

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . var_dump($reason->getMessage()) . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();

    }

    public function testSignIn()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, isset($responseBody->data->accessToken));


        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();

    }

    public function testSignInError()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, 'wrong password');

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, isset($responseBody->data->accessToken));


        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();
    }

    public function testPlatformCapabilities()
    {

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) use ($acrolinxEndPoint) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, isset($responseBody->data->accessToken));

            $acrolinxEndPoint->getCapabilities($responseBody->data->accessToken)->then(function (ResponseInterface $response) {
                $responseBody = json_decode($response->getBody());
                fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                    ' | StatusCode: ' . PHP_EOL));
                $this->assertEquals(true, isset($responseBody->data->checking));
                $this->assertEquals(true, isset($responseBody->data->document));

            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
                $this->assertEquals(true, $reason);
            });

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();


    }

    public function testPlatformCheckingCapabilities()
    {

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) use ($acrolinxEndPoint) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, isset($responseBody->data->accessToken));

            $acrolinxEndPoint->getCheckingCapabilities($responseBody->data->accessToken)->then(function (ResponseInterface $response) {
                $responseBody = json_decode($response->getBody());
                fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                    ' | StatusCode: ' . PHP_EOL));
                $this->assertEquals(true, isset($responseBody->data->guidanceProfiles));

            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
                $this->assertEquals(true, $reason);
            });

        }, function ( Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();
    }

    public function testCheckOptionsClass()
    {
        $checkOptions = new CheckOptions();

        $checkOptions->batchId = 1;
        $checkOptions->checkType = CheckType::baseline;
        $checkOptions->contentFormat = 'XML';
        $checkOptions->disableCustomFieldValidation = false;
        $checkOptions->reportTypes = array(ReportType::termHarvesting, ReportType::scorecard);
        $checkOptions->guidanceProfileId = '2';
        $checkOptions->languageId = 'en';
        $checkOptions->partialCheckRanges = new CheckRange(10, 20);

        $checkOptionsJson = $checkOptions->getJson();
        $this->assertEquals(false, empty((array)$checkOptionsJson));

    }

    public function testSubmitCheckWithCheckOptions()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) use ($acrolinxEndPoint) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $token = $responseBody->data->accessToken;
            $this->assertEquals(true, isset($responseBody->data->accessToken));

            $acrolinxEndPoint->getCheckingCapabilities($responseBody->data->accessToken)->then(function (ResponseInterface $response) use ($token, $acrolinxEndPoint) {
                $responseBody = json_decode($response->getBody());
                $this->assertEquals(true, isset($responseBody->data->guidanceProfiles));

                $guidanceProfileId = ($responseBody->data->guidanceProfiles)[0]->id;
                $this->assertEquals(true, isset($guidanceProfileId));
                fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                    ' | StatusCode: ' . PHP_EOL));

                $checkOptions = new CheckOptions();
                $checkOptions->batchId = 1;
                $checkOptions->checkType = CheckType::baseline;
                $checkOptions->contentFormat = 'TEXT';
                $checkOptions->disableCustomFieldValidation = false;
                $checkOptions->reportTypes = array(ReportType::termHarvesting, ReportType::scorecard);
                $checkOptions->guidanceProfileId = $guidanceProfileId;
                $checkOptions->languageId = 'en';
                //$checkOptions->partialCheckRanges = array(new CheckRange(10, 20));


                $checkRequest = new CheckRequest('Simple Test');
                $checkRequest->checkOptions = $checkOptions;
                $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
                $checkRequest->contentEncoding = ContentEncoding::none;

                $acrolinxEndPoint->check($token, $checkRequest)->then(function (ResponseInterface $response) {
                    $responseBody = json_decode($response->getBody());
                    $this->assertEquals(false, empty($responseBody->data->id));
                    fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                        ' | StatusCode: ' . PHP_EOL));

                }, function (Exception $reason) {
                    fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                        ' | StatusCode: ' . PHP_EOL));
                });

            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
                $this->assertEquals(true, $reason);
            }, function (Exception$reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
                $this->assertEquals(true, $reason);
            });

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();


    }

    protected function setUp(): void
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

    public function testSubmitCheckWithoutOptions()
    {

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) use ($acrolinxEndPoint) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $token = $responseBody->data->accessToken;
            $this->assertEquals(true, isset($responseBody->data->accessToken));

            $checkRequest = new CheckRequest('Simple Test');
            $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
            $checkRequest->contentEncoding = ContentEncoding::none;

            $acrolinxEndPoint->check($token, $checkRequest)->then(function (ResponseInterface $response) {
                $responseBody = json_decode($response->getBody());
                $this->assertEquals(false, empty($responseBody->data->id));
                fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                    ' | StatusCode: ' . PHP_EOL));

            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
            });

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();

    }

    public function testSubmitCheckAndPollForResult()
    {

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);

        $loop = Factory::create();

        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps(), $loop);
        $acrolinxEndPoint->signIn($ssoOptions)->then(function (ResponseInterface $response) use ($acrolinxEndPoint, &$loop) {
            $responseBody = json_decode($response->getBody());
            fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                ' | StatusCode: ' . PHP_EOL));
            $token = $responseBody->data->accessToken;
            $this->assertEquals(true, isset($responseBody->data->accessToken));

            $checkRequest = new CheckRequest('Verrry wrooong sentenceee');
            $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
            $checkRequest->contentEncoding = ContentEncoding::none;

            $acrolinxEndPoint->check($token, $checkRequest)->then(function (ResponseInterface $response) use ($acrolinxEndPoint, $token, &$loop) {
                $responseBody = json_decode($response->getBody());
                $this->assertEquals(false, empty($responseBody->data->id));
                fwrite(STDERR, print_r(PHP_EOL . $response->getStatusCode() .
                    ' | StatusCode: ' . PHP_EOL));

                $resultUrl = $responseBody->links->result;

                $poller = new Polling();
                $poller->poll($acrolinxEndPoint, $token, $loop, $resultUrl)->then(function (ResponseInterface $response) {
                    $responseBody = $response->getBody();
                    $checkResult = json_decode($responseBody);
                    $checkScore = $checkResult->data->quality->score;
                    $this->assertEquals(true, isset($checkScore));

                    fwrite(STDERR, print_r(PHP_EOL . $checkScore .
                        ' | CheckScore ' . PHP_EOL));
                }, function (Exception $reason) {
                    fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                        ' | StatusCode: ' . PHP_EOL));
                });

            }, function (Exception $reason) {
                fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                    ' | StatusCode: ' . PHP_EOL));
            });

        }, function (Exception $reason) {
            fwrite(STDERR, print_r(PHP_EOL . $reason->getMessage() .
                ' | StatusCode: ' . PHP_EOL));
            $this->assertEquals(true, $reason);
        });

        $loop->run();

    }
}
