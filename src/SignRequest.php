<?php

namespace Albertarni\TicketingPortalClient;

class SignRequest
{
    private $api_token;
    private $url;
    private $query_params;

    public function __construct($api_token, $url = '')
    {
        $this->api_token = $api_token;

        $this->setUrl($url);
    }
    public function setUrl($url)
    {
        $this->url = $url;
        $query     = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $this->query_params);
    }
    public function getUrl($data = array())
    {
        if (empty($data)) {
            return $this->url;
        }

        $this->url .= empty($this->query_params) ? '?' : '&';

        $this->url .= http_build_query($data);

        return $this->url;
    }
    public function makeHash($data = array())
    {
        $data           = array_merge($data, $this->query_params);
        $data = $this->array_dot($data);
        ksort($data);
        $string_to_hash = '';
        foreach ($data as $key => $value) {
            $string_to_hash .= "{$key}{$this->api_token}{$value}";
        }

        return sha1($string_to_hash);
    }
    public function validateHash($data = array())
    {
        $data  = array_merge($data, $this->query_params);
        $token = null;
        if (isset($data['sign_token'])) {
            $token = $data['sign_token'];
            unset($data['sign_token']);
        }

        return $this->makeHash($data) === $token;
    }

    /**
     * Flattens the array
     * @param $array
     * @param string $prepend
     * @return array
     */
    public function array_dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge($results, $this->array_dot($value, $prepend.$key.'.'));
            }
            else
            {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }
}
