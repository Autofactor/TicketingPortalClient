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
        $data                    = Input::except('redirect_url');
        $data['sign_project_id'] = $project_id;
        $data['sign_email']      = $signer->email();
        $data['sign_first_name'] = $signer->firstname();
        $data['sign_last_name']  = $signer->lastname();
        $data['sign_token']      = $sign_request->makeHash($data);

        $url = $sign_request->getUrl(array_merge($data, array(
            'sign_token' => $token
        )));

        return Redirect::away($url);
    }
    /**
     *
     * @return \Albertarni\TicketingPortalClient\SignerInterface
     */
    protected function getSignerInterfaceImplementation()
    {
        return I();
    }
    /**
     *
     * @param type $key
     * @return type
     */
    protected function getTicketingPortalConfig($key)
    {
        return Config::get('ticketingPortal.'.$key);
    }
}
