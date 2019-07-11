<?php


namespace Acrolinx\SDK\Models;


class PlatformInformation
{
    private $server;
    private $locales = array();


    public function __construct($platformInformation)
    {
        $this->server = new Server($platformInformation->server);
        $this->locales = $platformInformation->locales;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
    }


}

class Server
{
    private $version;
    private $name;

    public function __construct($server)
    {
        $this->version = $server->version;
        $this->name = $server->name;
    }


    public function getVersion(): string
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

}

