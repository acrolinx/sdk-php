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

use Exception;
use PHPUnit\Framework\TestCase;
use Dotenv;

class AcrolinxEndpointTest extends TestCase
{

    protected $acrolinxURL;
    protected $acrolinxAuthToken;
    protected $acrolinxSsoUser;
    protected $acrolinxPassword;

    // You'll get the clientSignature for your integration after a successful certification meeting.
    // See: https://support.acrolinx.com/hc/en-us/articles/205687652-Getting-Started-with-Custom-Integrations
    protected $DEVELOPMENT_SIGNATURE = 'SW50ZWdyYXRpb25EZXZlbG9wbWVudERlbW9Pbmx5';

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
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->getServerInfo();

        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $data = $responseJSON['data'];
        $status = $result['status'];

        $this->assertEquals(true, isset($data));
        $this->assertEquals(200, $status);
    }

    public function testGetServerInfoError()
    {
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, 'SomeFakeURL',
            'en', '');
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $message = '';
        try {
            $acrolinxEndPoint->getServerInfo();
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertContains('Could not resolve host', $message);
    }

    public function testSignIn()
    {
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');

        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);

        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $data = $responseJSON['data']['accessToken'];
        $status = $result['status'];
        // fwrite(STDERR, print_r($data, TRUE));

        $this->assertEquals(true, isset($data));
        $this->assertEquals(200, $status);
    }

    public function testSignInError()
    {
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');

        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, 'wrong password');
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);

        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $error = $responseJSON['error'];
        $status = $result['status'];

        // fwrite(STDERR, print_r($error, TRUE));

        $this->assertEquals(true, isset($error));
        $this->assertEquals(401, $status);
    }

    public function testPlatformCapabilities()
    {
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');

        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $accessToken = $responseJSON['data']['accessToken'];
        $this->assertEquals(true, isset($accessToken));

        //fwrite(STDERR, print_r('accessToken: ' . $accessToken, TRUE));

        $result =  $acrolinxEndPoint->getCapabilities($accessToken);
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $checking= $responseJSON['data']['checking'];
        $document= $responseJSON['data']['document'];
        $status = $result['status'];
        //fwrite(STDERR, print_r($data, TRUE));

        $this->assertEquals(true, isset($checking));
        $this->assertEquals(true, isset($document));
        $this->assertEquals(200, $status);

    }

    public function testPlatformCheckingCapabilities()
    {
        $props = new AcrolinxEndPointProps($this->DEVELOPMENT_SIGNATURE, $this->acrolinxURL,
            'en', '');

        // fwrite(STDERR, print_r('user' . $this->acrolinxSsoUser, TRUE));
        // fwrite(STDERR, print_r('password' . $this->acrolinxPassword, TRUE));

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $accessToken = $responseJSON['data']['accessToken'];
        $this->assertEquals(true, isset($accessToken));
        //fwrite(STDERR, print_r('accessToken: ' . $accessToken, TRUE));

        $result =  $acrolinxEndPoint->getCheckingCapabilities($accessToken);
        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $guidanceProfiles= $responseJSON['data']['guidanceProfiles'];
        $status = $result['status'];
        //fwrite(STDERR, print_r($data, TRUE));

        $this->assertEquals(true, isset($guidanceProfiles));
        $this->assertEquals(200, $status);

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

    public function testBasicCheckSubmition()
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
        $checkOptions->partialCheckRanges = array(new CheckRange(10, 20));


        $checkRequest = new CheckRequest('Simple Test');
        $checkRequest->checkOptions = $checkOptions;
        $checkRequest->document = new DocumentDescriptorRequest('abc.txt');
        $checkRequest->contentEncoding = ContentEncoding::none;


        //fwrite(STDERR, print_r($checkRequest->getJson(), TRUE));

        $result = $acrolinxEndPoint->check($accessToken, $checkRequest);
        fwrite(STDERR, print_r($result, TRUE));


    }

}
