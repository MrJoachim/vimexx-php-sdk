<?php

namespace MrJoachim\VimexxPhpSdk;

use GuzzleHttp\Client;
use MrJoachim\VimexxPhpSdk\Entities\Domain;

class VimexxSDK
{
    /**
     * Endpoint of the Vimexx API.
     */
    public const API_ENDPOINT = "https://api.vimexx.nl";

    /**
     * HTTP Get Method
     * 
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * HTTP Post Method
     * 
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * HTTP PUT Method
     * 
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * The WHMCS version.
     */
    public const WHMCS_VERSION = "8.4.0-release.1";

    /**
     * @var string;
     */
    private $apiEndpoint = SELF::API_ENDPOINT;

    /**
     * @var string;
     */
    protected $accessToken;

    /**
     * @var int;
     */
    protected $clientId;

    /**
     * @var string;
     */
    protected $clientKey;

    /**
     * @var string;
     */
    protected $username;

    /**
     * @var string;
     */
    protected $password;

    /**
     * @var bool;
     */
    protected $testMode;


    public function __construct($clientId, $clientKey, $username, $password)
    {
        $this->clientId = $clientId;
        $this->clientKey = $clientKey;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Enable the test mode.
     * 
     * @param bool $testModeEnabled
     * @return VimexxSDK
     */
    public function enableTestMode($testModeEnabled = true)
    {
        $this->testMode = $testModeEnabled;
        return $this;
    }

    /**
     * Get the api url with optional path.
     * 
     * @param string $path
     * 
     * @return string
     */
    function getApiUrl($path = "")
    {
        return ($this->testMode ? $this->apiEndpoint . '/apitest/v1' : $this->apiEndpoint . '/api/v1') . $path;
    }

    private function getAccessToken()
    {

        if (!$this->accessToken) {
            $client = new Client(['base_uri' => $this->apiEndpoint]);
            $response = $client->request('POST', '/auth/token', [
                "form_params" => [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientKey,
                    'username' => $this->username,
                    'password' => $this->password,
                    'scope' => 'whmcs-access',
                ]
            ]);
            $this->accessToken = json_decode($response->getBody(), true)['access_token'];
        }
        return $this->accessToken;
    }

    /**
     * Send a request to the Vimexx API
     * 
     * @param string $method
     * @param string $path
     * @param array $data
     * 
     * @return array
     */
    function request($method, $path, $data = [])
    {
        $client = new Client();

        $response = $client->request($method, $this->getApiUrl($path), [
            "headers" => ['Authorization' => "Bearer " . $this->getAccessToken()],
            "json" => [
                'body' => $data,
                'version' => SELF::WHMCS_VERSION
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get a domain entity by the sld and tld.
     * 
     * @param string $sld
     * @param string $tld
     * 
     * @return Domain
     */
    public function getDomain($sld, $tld)
    {
        return Domain::fromApi($this, ["sld" => $sld, "tld" => $tld, ...$this->request(SELF::METHOD_POST, "/whmcs/domain/sync", [
            'sld' => $sld,
            'tld' => $tld
        ])["data"]]);
    }

    /**
     * Register a domain by the sld and tld.
     * Returns true if success.
     * 
     * @param string $sld
     * @param string $tld
     * 
     * @return bool
     */
    public function registerDomain($sld, $tld)
    {
        return $this->request(SELF::METHOD_POST, "/whmcs/domain/register", [
            'sld' => $sld,
            'tld' => $tld
        ])["result"] ? true : false;
    }
}
