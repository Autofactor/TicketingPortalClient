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

    public function getUrl($url, $custom_data = array())
    {
        foreach ($custom_data as $key => $value) {
            $data["sign_{$key}"] = $value;
        }

        $sign_request            = new SignRequest($this->api_token, $url);
        $data['sign_project_id'] = $this->project_id;
        $data['sign_email']      = $this->signer->helpdeskEmail();
        $data['sign_first_name'] = $this->signer->helpdeskFirstname();
        $data['sign_last_name']  = $this->signer->helpdeskLastname();
        $data['sign_token']      = $sign_request->makeHash($data);

        return $sign_request->getUrl($data);
    }

}
