<?php

namespace Albertarni\TicketingPortalClient;

class UrlGenerator
{
    private $signer;
    private $api_token;
    private $project_id;

    public function __construct(SignerInterface $signer, $api_token, $project_id)
    {
        $this->signer     = $signer;
        $this->api_token  = $api_token;
        $this->project_id = $project_id;
    }

    public function getUrl($url, $body_data = [])
    {
        foreach ($this->signer->helpdeskData() as $key => $value) {
            $data["sign_{$key}"] = $value;
        }

        $sign_request            = new SignRequest($this->api_token, $url);
        $data['sign_project_id'] = $this->project_id;
        $data['sign_token']      = $sign_request->makeHash(array_merge($data, (array)$body_data));

        return $sign_request->getUrl($data);
    }

}
