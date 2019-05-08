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

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Dotenv;
use Acrolinx\SDK\Exceptions\AcrolinxServerException;
use Acrolinx\SDK\Models\AcrolinxEndPointProps;
use Acrolinx\SDK\Models\SsoSignInOptions;
use Acrolinx\SDK\Models\CheckOptions;
use Acrolinx\SDK\Models\CheckType;
use Acrolinx\SDK\Models\ReportType;
use Acrolinx\SDK\Models\CheckRange;
use Acrolinx\SDK\Models\CheckRequest;
use Acrolinx\SDK\Models\DocumentDescriptorRequest;
use Acrolinx\SDK\Models\ContentEncoding;
use PHPUnit\Runner\Exception;
use Psr\Http\Message\ResponseInterface;


class AcrolinxEndpointTest extends TestCase
{

    protected $acrolinxURL;
    protected $acrolinxAuthToken;
    protected $acrolinxSsoUser;
    protected $acrolinxPassword;

    // You'll get the clientSignature for your integration after a successful certification meeting.
    // See: https://support.acrolinx.com/hc/en-us/articles/205687652-Getting-Started-with-Custom-Integrations
    protected $DEVELOPMENT_SIGNATURE = 'SW50ZWdyYXRpb25EZXZlbG9wbWVudERlbW9Pbmx5';

    private function getProps()
    {
        return new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');
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

    /**
     * Test get server info API
     */
    public function testGetServerInfo()
    {
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        $response = $acrolinxEndPoint->getServerInfo()->wait(true);
        $result = json_decode($response->getBody()->getContents(), true);
        $data = $result['data'];
        // fwrite(STDERR, print_r(PHP_EOL . var_dump($result) . PHP_EOL));
        $this->assertEquals(true, isset($data));
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
    }


    public function testGetServerInfoError()
    {
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, 'SomeFakeURL',
            'en', '');
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        try {
            $acrolinxEndPoint->getServerInfo()->wait();
        } catch (RequestException $e) {
            $message = $e->getMessage();
            $this->assertContains('cURL error 3: <url> malformed', $message);
        }

    }

    public function testSignIn()
    {
        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        try {
            $response = $acrolinxEndPoint->signIn($ssoOptions)->wait(true);
            $responseBody = json_decode($response->getBody());
            $this->assertEquals(true, isset($responseBody->data->accessToken));

        } catch (RequestException $e) {
            fwrite(STDERR, print_r(PHP_EOL . $e->getMessage() .
                ' | StatusCode: ' . $e->getCode() . PHP_EOL));
        }

    }

    /*public function testSignInError()
    {
        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, 'wrong password');
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        $error = NULL;
        try {
            $acrolinxEndPoint->signIn($ssoOptions);
        } catch (AcrolinxServerException $e) {
            //   fwrite(STDERR, print_r(PHP_EOL . $e->getMessage() .
            //       ' | StatusCode: ' . $e->getStatus() . PHP_EOL));
            $error = $e;
        }
        $this->assertEquals(true, isset($error));
        $this->assertEquals(401, $error->getStatus());
    }

    public function testPlatformCapabilities()
    {
        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        $result = $acrolinxEndPoint->signIn($ssoOptions);
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $accessToken = $responseJSON['data']['accessToken'];
        $this->assertEquals(true, isset($accessToken));

        //fwrite(STDERR, print_r('accessToken: ' . $accessToken, TRUE));
        try {
            $result = $acrolinxEndPoint->getCapabilities($accessToken);
        } catch (AcrolinxServerException $e) {
            fwrite(STDERR, print_r(PHP_EOL . $e->getMessage() .
                ' | StatusCode: ' . $e->getStatus() . PHP_EOL));
        }
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $checking = $responseJSON['data']['checking'];
        $document = $responseJSON['data']['document'];
        $status = $result['status'];
        //fwrite(STDERR, print_r($data, TRUE));

        $this->assertEquals(true, isset($checking));
        $this->assertEquals(true, isset($document));
        $this->assertEquals(200, $status);

    }

    public function testPlatformCheckingCapabilities()
    {
        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInOptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        $result = $acrolinxEndPoint->signIn($ssoOptions);
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $accessToken = $responseJSON['data']['accessToken'];
        $this->assertEquals(true, isset($accessToken));
        //fwrite(STDERR, print_r('accessToken: ' . $accessToken, TRUE));
        try {
            $result = $acrolinxEndPoint->getCheckingCapabilities($accessToken);
        } catch (AcrolinxServerException $e) {
            fwrite(STDERR, print_r(PHP_EOL . $e->getMessage() .
                ' | StatusCode: ' . $e->getStatus() . PHP_EOL));
        }
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $guidanceProfiles = $responseJSON['data']['guidanceProfiles'];
        $status = $result['status'];
        //fwrite(STDERR, print_r($data, TRUE));

        $this->assertEquals(true, isset($guidanceProfiles));
        $this->assertEquals(200, $status);
    }

    public function testGetAuthTokenFromSSOCredentials()
    {
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        $result = $acrolinxEndPoint->getAuthTokenFromSSOCredentials($this->acrolinxSsoUser, $this->acrolinxPassword);
        $this->assertTrue(is_string($result));
        $this->assertTrue(strlen($result) > 10);
    }

    public function testGetAuthTokenFromSSOCredentialsErrorCase()
    {
        $acrolinxEndPoint = new AcrolinxEndpoint($this->getProps());
        $error = NULL;
        try {
            $acrolinxEndPoint->getAuthTokenFromSSOCredentials($this->acrolinxSsoUser, 'wrongPassword');

        } catch (AcrolinxServerException $e) {
            $error = $e;
        }
        $this->assertTrue(isset($error));
        $message = $error->getMessage();
        $this->assertTrue(isset($message));
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
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');

        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);
        $accessToken = json_decode($result['response'], true)['data']['accessToken'];
        $this->assertEquals(true, isset($accessToken));

        $checkingCapabilities = $acrolinxEndPoint->getCheckingCapabilities($accessToken);
        $guidanceProfileId = json_decode($checkingCapabilities['response'], true)['data']['guidanceProfiles'][0]['id'];
        $this->assertEquals(true, isset($guidanceProfileId));


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

        $result = $acrolinxEndPoint->check($accessToken, $checkRequest);
        $checkId = json_decode($result['response'], true)['data']['id'];

        $this->assertEquals(false, empty($checkId));

    }

    public function testSubmitCheckWithoutOptions()
    {

        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');

        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);
        $accessToken = json_decode($result['response'], true)['data']['accessToken'];
        $this->assertEquals(true, isset($accessToken));

        $checkRequest = new CheckRequest('Simple Test');

        $result = $acrolinxEndPoint->check($accessToken, $checkRequest);
        $checkId = json_decode($result['response'], true)['data']['id'];

        $this->assertEquals(false, empty($checkId));

    }*/

}
