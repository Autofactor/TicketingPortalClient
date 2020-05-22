<?php

namespace Autofactor\TicketingPortalClient;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

trait RedirectToPortalTrait
{
    /**
     * Redirect to the ticketing portal
     * @param \Autofactor\TicketingPortalClient\SignerInterface $signer
     * @return type
     */
    public function redirectToTicketingPortal($redirect_url)
    {
        $signer     = $this->getSignerInterfaceImplementation();
        $api_token  = $this->getTicketingPortalConfig('apiToken');
        $project_id = $this->getTicketingPortalConfig('projectId');

        $url_generator = new UrlGenerator($signer, $api_token, $project_id);

        return Redirect::away($url_generator->getUrl($redirect_url));
    }
    /**
     *
     * @return \Autofactor\TicketingPortalClient\SignerInterface
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
