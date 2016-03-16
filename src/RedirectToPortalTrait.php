<?php

namespace Albertarni\TicketingPortalClient;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

trait RedirectToPortalTrait
{
    /**
     * Redirect to the ticketing portal
     * @param \Albertarni\TicketingPortalClient\SignerInterface $signer
     * @return type
     */
    public function redirectToTicketingPortal($redirect_url)
    {
        $signer     = $this->getSignerInterfaceImplementation();
        $api_token  = $this->getTicketingPortalConfig('apiToken');
        $project_id = $this->getTicketingPortalConfig('projectId');

        $sign_request            = new SignRequest($api_token, $redirect_url);
        $data['sign_project_id'] = $project_id;
        $data['sign_email']      = $signer->helpdeskEmail();
        $data['sign_first_name'] = $signer->helpdeskFirstname();
        $data['sign_last_name']  = $signer->helpdeskLastname();
        $data['sign_token']      = $sign_request->makeHash($data);

        $url = $sign_request->getUrl($data);

        return Redirect::away($url);
    }
    /**
     *
     * @return \Albertarni\TicketingPortalClient\SignerInterface
     */
    protected abstract function getSignerInterfaceImplementation();
    /**
     *
     * @param type $key
     * @return type
     */
    protected function getTicketingPortalConfig($key)
    {
        return Config::get('ticketing-portal-client::config.'.$key);
    }
}
