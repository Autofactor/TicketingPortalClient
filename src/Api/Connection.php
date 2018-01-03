<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 4:05 PM
 */

namespace Albertarni\TicketingPortalClient\Api;

use Albertarni\TicketingPortalClient\Api\Exception\ForbiddenException;
use Albertarni\TicketingPortalClient\Api\Exception\NotFoundException;
use Albertarni\TicketingPortalClient\Api\Exception\UnauthorizedException;
use Albertarni\TicketingPortalClient\Api\Exception\ValidationException;
use Albertarni\TicketingPortalClient\SignerInterface;
use Albertarni\TicketingPortalClient\UrlGenerator;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
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
        return $this->send('GET', $url, [], $params);
    }

    /**
     * @param $url
     * @param $body
     * @return mixed
     */
    public function post($url, $body)
    {
        return $this->send('POST', $url, $body);
    }

    /**
     * @param $url
     * @param $body
     * @return mixed
     */
    public function put($url, $body)
    {
        return $this->send('PUT', $url, $body);
    }


    /**
     * @param $url
     * @return mixed
     */
    public function delete($url)
    {
        return $this->send('DELETE', $url);
    }


    /**
     * @param $method
     * @param $url
     * @param array $body
     * @param array $params
     * @return mixed
     * @throws Exception
     * @throws ValidationException
     */
    private function send($method, $url, $body = [], $params = []) {
        $url = $this->formatUrl($url);

        $request = $this->createRequest($method, $url, $body, $params);

        try {
            $response = $this->client()->send($request);
        }
        catch (ClientException $e) {
            $json = $this->parseResponse($e->getResponse());
            switch($e->getCode()) {
                case 401:
                    throw new UnauthorizedException($json);
                    break;
                case 403:
                    throw new ForbiddenException($json['error']);
                    break;
                case 404:
                    throw new NotFoundException();
                    break;
                case 422:
                    throw new ValidationException($json);
                    break;
            }
        }

        return $this->parseResponse($response);
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
     * @throws ValidationException
     */
    private function parseResponse(Response $response)
    {
        if ($response->getStatusCode() === 204) {
            return null;
        }

        Psr7\rewind_body($response);
        $json = json_decode($response->getBody()->getContents(), true);

        return $json;
    }


    private function formatUrl($endPoint)
    {
        return "{$this->baseUrl}api/{$endPoint}";
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
