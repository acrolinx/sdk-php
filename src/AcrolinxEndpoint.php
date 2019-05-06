<?php namespace Acrolinx\SDK;

class AcrolinxEndpoint
{
    /**  @var string $serverAddress set the Platform URL to talk to */
    private $serverAddress = '';
    private $clientLocale = 'en';
    private $props = null;

    /**
     * AcrolinxEndpoint constructor.
     * @param $serverAddress
     */
    public function __construct($props)
    {
        $this->props = $props;
    }

    /**
     * @param $clientLocale
     * Sets the language interface.
     */
    public function setClientLocale($clientLocale)
    {
        $this->clientLocale = $clientLocale;
    }

    /**
     * Get server information
     */
    public function getServerInfo()
    {

    }

    /**
     * @return array
     * Get common headers
     */
    public function getCommonHeaders()
    {
        return array(
            'X-Acrolinx-Client: ' . $this->props->clientSignature,
            'X-Acrolinx-Client-Locale: ' . $this->props->clientLocale,
            'X-Acrolinx-Base-Url:' . $this->props->baseUrl,
            'Content-Type: application/json'
        );
    }

    /**
     * @param $options
     */
    public function signin($options)
    {

    }

    /**
     * @param $authToken
     */
    public function getCapabilities($authToken)
    {

    }

    /**
     * @param $path
     * @param $authToken
     * @param $data
     * POST method
     */
    public function post($path, $authToken, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_URL, $this->props->serverAddress . $path);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getCommonHeaders());


    }


}
