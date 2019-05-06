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

use Dotenv;
use PHPUnit\Framework\TestCase;

class AcrolinxEndpointTest extends TestCase
{

    protected $acrolinxURL;
    protected $acrolinxAuthToken;
    protected $acrolinxSsoUser;
    protected $acrolinxPassword;

    protected function setUp(): void
    {
        $parent_dir = dirname(__DIR__);
        $dotenv = Dotenv\Dotenv::create($parent_dir);
        $dotenv->load();
        $serverEnv = getenv('ACROLINX_TEST_SERVER_URL');
        $tokenEnv = getenv('ACROLINX_ACCESS_TOKEN');
        $ssoUser = getenv('ACROLINX_SSO_USER');
        $ssoPassword = getenv('ACROLINX_SSO_PASSWORD');
        if (isset($serverEnv)) {
            $this->acrolinxURL = $serverEnv;
        } else {
            echo 'No Acrolinx Server Address set.';
        }
        if (isset($tokenEnv)) {
            $this->acrolinxAuthToken = $tokenEnv;
        } else {
            echo 'No Acrolinx Auth Token set.';
        }
        if (isset($ssoUser)) {
            $this->acrolinxSsoUser = $ssoUser;
        } else {
            echo 'No Acrolinx SSO user set';
        }
        if (isset($ssoPassword)) {
            $this->acrolinxPassword = $ssoPassword;
        } else {
            echo 'No Acrolinx SSO password set';
        }

    }

    /**
     * Test get server info API
     */
    public function testGetServerInfo()
    {
        $props = new AcrolinxEndPointProps('dummySignature', $this->acrolinxURL,
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

    public function testSignIn()
    {
        $props = new AcrolinxEndPointProps('dummySignature', $this->acrolinxURL,
            'en', '');

        $ssoOptions = new SsoSignInoptions($this->acrolinxSsoUser, $this->acrolinxPassword);
        $acrolinxEndPoint = new AcrolinxEndpoint($props);
        $result = $acrolinxEndPoint->signIn($ssoOptions);

        $response = $result['response'];
        $responseJSON = json_decode($response, true);
        $data = $responseJSON['data'];
        $status = $result['status'];

        $this->assertEquals(true, isset($data));
        $this->assertEquals(200, $status);
    }

}
