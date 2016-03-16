<?php

namespace Albertarni\TicketingPortalClient;

use Illuminate\Support\ServiceProvider;
use Config;
use Route;
use Request;
use Session;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['config']->package('albertarni/ticketing-portal-client', __DIR__.'/config');
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Route::filter('sign_request', function() {
            if (Request::has('sign_token')) {
                $redirect_url = Request::get('redirect_url');
                $apiToken     = $this->app['config']->get('ticketing-portal-client::config.apiToken');
                $sign_request = new SignRequest($apiToken);
                if ($sign_request->validateHash(Request::all())) {
                    Session::put('redirect_url', $redirect_url);
                }
            }
        });
    }
}
