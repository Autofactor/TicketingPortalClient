<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 4:05 PM
 */

namespace Albertarni\TicketingPortalClient\Api;

use Albertarni\TicketingPortalClient\SignerInterface;
use Albertarni\TicketingPortalClient\UrlGenerator;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;

/**
 * Class Connection
 *
 * @package Albertarni\TicketingPortalClient
 *
 */
class Connection
{
    /**
     * @var string
     */
    private $baseUrl = 'https://support.autofactor.eu/';

    /**
     * The api token used to sign the requests.
     */
    private $api_token;

    /**
     * @var Client
     */
    private $client;

    private $signer = null;
    private $project_id = null;

    /**
     *
     */
    protected $middleWares = [];


    /**
     * Sets the api token to the be used by all requests
     *
     * Connection constructor.
     * @param string $api_token
     */
    public function __construct($api_token, $project_id)
    {
        $this->setApiToken($api_token);
        $this->setProjectId($project_id);
    }


    /**
     * @return Client
     */
    private function client()
    {
        if ($this->client) {
            return $this->client;
        }

        $handlerStack = HandlerStack::create();
        foreach ($this->middleWares as $middleWare) {
            $handlerStack->push($middleWare);
        }

        $this->client = new Client([
            'http_errors' => true,
            'handler' => $handlerStack,
        ]);

        return $this->client;
    }


    /**
     * Adds a new middleware to the request
     * @param $middleWare
     */
    public function insertMiddleWare($middleWare)
    {
        $this->middleWares[] = $middleWare;
    }

    /**
     * Creates new Client to be use for requests
     * @return Client
     * @throws Exception
     */
    public function connect()
    {
        if ($this->needsAuthentication()) {
            throw new Exception('Api token not defined');
        }

        $client = $this->client();

        return $client;
    }

    /**
     * @param string $method
     * @param $endpoint
     * @param null $body
     * @param array $params
     * @param array $headers
     * @return Request
     */
    private function createRequest($method = 'GET', $endpoint, $body = [], array $params = [], array $headers = [])
    {
        if (empty($this->signer))
        {
            throw new Exception('Requester not set');
        }

        if (empty($this->project_id))
        {
            throw new Exception('Project not set');
        }

        // Add default json headers to the request
        $headers = array_merge($headers, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        $url_generator = new UrlGenerator($this->signer, $this->api_token, $this->project_id);

        // Create param string
        if (!empty($params)) {
            $params['project_id'] = $this->project_id;
            $endpoint .= '?' . http_build_query($params);
        }

        $body = (array)$body;
        if (!empty($body)) {
            $body['project_id'] = $this->project_id;
        }
        $endpoint = $url_generator->getUrl($endpoint,$body);

        // Create the request
        $request = new Request($method, $endpoint, $headers, json_encode($body));

        return $request;
    }

    /**
     * @param $url
     * @param array $params
     * @return mixed
     */
    public function get($url, array $params = [])
    {
        $url = $this->formatUrl($url);

        try {
            $request = $this->createRequest('GET', $url, [], $params);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }

    /**
     * @param $url
     * @param $body
     * @return mixed
     */
    public function post($url, $body)
    {
        $url = $this->formatUrl($url);

        try {
            $request  = $this->createRequest('POST', $url, $body);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }

    /**
     * @param $url
     * @param $body
     * @return mixed
     * @throws ApiException
     */
    public function put($url, $body)
    {
        $url = $this->formatUrl($url);

        try {
            $request  = $this->createRequest('PUT', $url, $body);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }


    /**
     * @param $url
     * @return mixed
     * @throws ApiException
     */
    public function delete($url)
    {
        $url = $this->formatUrl($url);

        try {
            $request  = $this->createRequest('DELETE', $url);
            $response = $this->client()->send($request);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            $this->parseExceptionForErrorMessages($e);
        }
    }



    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->api_token;
    }


    /**
     * @param mixed $accessToken
     */
    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;
    }


    /**
     * @return bool
     */
    public function needsAuthentication()
    {
        return empty($this->api_token);
    }


    /**
     * @param Response $response
     * @return mixed
     * @throws ApiException
     */
    private function parseResponse(Response $response)
    {
        try {

            if ($response->getStatusCode() === 204) {
                return [];
            }

            Psr7\rewind_body($response);
            $json = json_decode($response->getBody()->getContents(), true);

            return $json;
        } catch (\RuntimeException $e) {
            throw new ApiException($e->getMessage());
        }
    }


    private function formatUrl($endPoint)
    {
        return "{$this->baseUrl}{$endPoint}";
    }


    /**
     * Parse the reponse in the Exception to return the Exact error messages
     * @param Exception $e
     * @throws ApiException
     */
    private function parseExceptionForErrorMessages(Exception $e)
    {
        if (! $e instanceof BadResponseException) {
            throw new ApiException($e->getMessage());
        }

        $response = $e->getResponse();
        Psr7\rewind_body($response);
        $responseBody = $response->getBody()->getContents();
        $decodedResponseBody = json_decode($responseBody, true);

        if (! is_null($decodedResponseBody) && isset($decodedResponseBody['error']['message']['value'])) {
            $errorMessage = $decodedResponseBody['error']['message']['value'];
        } else {
            $errorMessage = $responseBody;
        }

        throw new ApiException('Error ' . $response->getStatusCode() .': ' . $errorMessage);
    }


    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }


    /**
     * Set base URL
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }


    /**
     * Sets the entity that is requesting the request
     * @param SignerInterface $signer
     */
    public function setSigner(SignerInterface $signer) {
        $this->signer = $signer;
    }


    /**
     * @param integer $project_id
     */
    public function setProjectId($project_id) {
        $this->project_id = $project_id;
    }
}
