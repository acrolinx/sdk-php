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


namespace Acrolinx\SDK;


class SsoSignInoptions
{
    private $usernameKey;
    private $passwordKey;
    private $userId;
    private $password;

    public function __construct($userId = '', $password = '', $usernameKey = 'username', $passwordKey = 'password')
    {
        $this->usernameKey = $usernameKey;
        $this->passwordKey = $passwordKey;
        $this->userId = $userId;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsernameKey(): string
    {
        return $this->usernameKey;
    }

    /**
     * @return mixed
     */
    public function getPasswordKey(): string
    {
        return $this->passwordKey;
    }

    /**
     * @return mixed
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getPassword(): string
    {
        return $this->password;
    }


}
