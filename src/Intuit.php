<?php

namespace Vizad;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\Response;
use League\OAuth1\Client\Credentials\CredentialsInterface;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Signature\SignatureInterface;

/**
 * Quickbooks allows authentication via Quickbooks OAuth.
 *
 * In order to use Quickbooks OAuth you must register your application at <https://developer.intuit.com/>.
 *
 * Example application configuration:
 *
 * ~~~
 * use Vizad\Intuit;
 * define('QB_CUSTOMER_ID', 'xxxxxxxxxxxxxxxxxxxxxxxxxxx');
 * define('QB_CUSTOMER_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
 * 
 * $intuitClient = new Intuit([
 *             'identifier'   => QB_CUSTOMER_ID,
 *             'secret'       => QB_CUSTOMER_SECRET,
 *             'callback_uri' => "http://CALLBACK_URI",
 *             'sandboxMode' => true
 *         ]);
 * ~~~
 *
 * @see https://developer.intuit.com/apis
 * @see https://developer.intuit.com/v2/apiexplorer
 * @see https://developer.intuit.com/v2/ui#/sandbox for Sandbox
 * @see https://developer.intuit.com/docs/0100_accounting/0002_create_an_app
 * @see https://developer.intuit.com/blog/2015/02/19/oauth-for-intuit-demystified
 * @see https://developer.intuit.com/docs/0100_accounting/0050_your_first_request/0200_first_request_with_the_api_explorer
 *
 * @author Vishnu Prasad <vishnuprasad.pg@gmail.com>
 *
 */

class Intuit extends Server {

    public $sandboxMode = false;

    public $apiBaseUrl = 'https://quickbooks.api.intuit.com';

    protected $responseType = 'xml';

    public $disconnectUrl = 'https://appcenter.intuit.com/api/v1/connection/disconnect';

    public $reconnectUrl = 'https://appcenter.intuit.com/api/v1/connection/reconnect';


    public function __construct (array $clientCredentialsInterface, SignatureInterface $signature = null){

        parent::__construct($clientCredentialsInterface, $signature);

        if($clientCredentialsInterface["sandboxMode"] == true){
            $this->apiBaseUrl = 'https://sandbox-quickbooks.api.intuit.com';
        }
    }

    public function urlTemporaryCredentials()
    {
        return 'https://oauth.intuit.com/oauth/v1/get_request_token';
    }

    public function urlAuthorization()
    {
        return 'https://appcenter.intuit.com/Connect/Begin';
    }

    public function urlTokenCredentials()
    {
        return 'https://oauth.intuit.com/oauth/v1/get_access_token';
    }

    public function urlUserDetails()
    {
        return 'https://appcenter.intuit.com/api/v1/user/current';
    }

    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return;
    }

    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return (string) $data->User->EmailAddress;
    }

    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return;
    }

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $user = new User;

        $user->firstName = (string) $data->User->FirstName;
        $user->lastName  = (string) $data->User->LastName;
        $user->name      = $user->firstName . ' ' . $user->lastName;
        $user->email     = (string) $data->User->EmailAddress;

        $verified = filter_var((string) $data->User->IsVerified, FILTER_VALIDATE_BOOLEAN);

        $user->extra = compact('verified');

        return $user;
    }

    public function getUserInfo(TokenCredentials $tokenCredentials)
    {
        return $this->api($tokenCredentials, "https://appcenter.intuit.com/api/v1/user/current");
    }

    public function getCompanyInfo(TokenCredentials $tokenCredentials, $realmId)
    {
        return $this->api($tokenCredentials, "v3/company/".$realmId."/companyinfo/".$realmId);
    }

    public function apiDisconnect(TokenCredentials $tokenCredentials)
    {
        return $this->api($tokenCredentials, $this->disconnectUrl);
    }

    public function apiReconnect(TokenCredentials $tokenCredentials)
    {
        return $this->api($tokenCredentials, $this->reconnectUrl);
    }


    public function api(TokenCredentials $tokenCredentials, $apiUrl, $method = "get", $data = [], $additionalHeaders =
    [])
    {
        $url = $this->getApiUrl($apiUrl);
        $client = $this->createHttpClient();
        $headers = $this->getHeaders($tokenCredentials, $method, $url);
        $headers = array_merge($headers, $additionalHeaders);
        $result = "";
        try {
            switch(strtolower($method)){
                case 'post':
                    $response = $client->post($url, $headers, $data)->send();
                    $result = $this->getResponse($response);
                    break;
                case 'get':
                    $response = $client->get($url, $headers)->send();
                    $result = $this->getResponse($response);
                    break;
            }
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $body = $response->getBody();
            $statusCode = $response->getStatusCode();

            //throw new \Exception

            return (
                "Received error [$body] with status code [$statusCode]."
            );
        }

        return $result;

    }

    private function getApiUrl($apiUrl)
    {
        if(strstr($apiUrl, "http")){
            return $apiUrl;
        }
        $apiUrl = ltrim($apiUrl, "/");
        return $this->apiBaseUrl . "/" . $apiUrl;
    }

    private function getResponse(Response $response)
    {
        $result = "";
        switch ($this->responseType) {
            case 'json':
                $result = $response->json();
                break;

            case 'xml':
                $result = $response->xml();
                $result = $this->xml2array($result);
                break;

            case 'string':
                parse_str($response->getBody(), $result);
                break;

            default:
                throw new \InvalidArgumentException("Invalid response type [{$this->responseType}].");
        }

        return $result;
    }

    protected function xml2array ($xmlObject, $out = array ())
    {
        $out = json_decode(json_encode($xmlObject), true);
        return $out;
    }

}