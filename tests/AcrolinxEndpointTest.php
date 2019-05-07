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
        try {
            $result = $acrolinxEndPoint->getServerInfo();
        } catch (AcrolinxServerException $e) {
            $message = $e->getMessage();
            fwrite(STDERR, print_r(PHP_EOL . $e->getMessage() .
                ' | StatusCode: ' . $e->getStatus() . PHP_EOL));
        }
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
        } catch (AcrolinxServerException $e) {
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
        try {
            $result = $acrolinxEndPoint->signIn($ssoOptions);
        } catch (AcrolinxServerException $e) {
            fwrite(STDERR, print_r(PHP_EOL . $e->getMessage() .
                ' | StatusCode: ' . $e->getStatus() . PHP_EOL));
        }
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

}
