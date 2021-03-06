<?php

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


namespace Acrolinx\SDK\Models;


use MyCLabs\Enum\Enum;
use Psr\Http\Message\ResponseInterface;

class SignInSuccessData
{
    private $accessToken;
    private $user;
    private $integration;
    private $authorizedUsing;


    public function __construct(ResponseInterface $response)
    {
        $responseBody = json_decode($response->getBody());
        $this->accessToken = $responseBody->data->accessToken;
        $this->user = $responseBody->data->user;
        $this->integration = new Integration($responseBody->data->integration);
        $this->authorizedUsing = $responseBody->data->authorizedUsing;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return Integration
     */
    public function getIntegration(): Integration
    {
        return $this->integration;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return AuthorizationType
     */
    public function getAuthorizedUsing(): AuthorizationType
    {
        return $this->authorizedUsing;
    }

}

class User
{
    private $id;
    private $username;

    public function __construct($user)
    {
        $this->id = $user->id;
        $this->username = $user->username;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}

class Integration
{
    private $properties = array();
    private $addons = array();

    public function __construct($integration)
    {
        $this->properties = $integration->properties;
        $this->addons = $integration->addons;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getAddons(): array
    {
        return $this->addons;
    }
}

class AuthorizationType extends Enum
{
    const ACROLINX_SSO = 'ACROLINX_SSO';
    const ACROLINX_SIGN_IN = 'ACROLINX_SIGN_IN';
    const ACROLINX_TOKEN = 'ACROLINX_TOKEN';
}
