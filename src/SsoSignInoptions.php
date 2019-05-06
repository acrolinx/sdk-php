<?php


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