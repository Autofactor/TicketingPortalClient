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
        ksort($data);
        $string_to_hash = '';
        foreach ($data as $key => $value) {
            $string_to_hash .= "{$key}{$this->api_token}{$value}";
        }

        return sha1($string_to_hash);
    }
    public function validateHash($data = [])
    {
        $data  = array_merge($data, $this->query_params);
        $token = null;
        if (isset($data['sign_token'])) {
            $token = $data['sign_token'];
            unset($data['sign_token']);
        }

        return $this->makeHash($data) === $token;
    }
}
