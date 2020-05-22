<?php

namespace Autofactor\TicketingPortalClient;

use Illuminate\Support\ServiceProvider;
use Config;
use Route;
use Request;
use Session;
use App;
use Redirect;

class PackageServiceProvider5 extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('ticketing-portal-client.php'),
        ]);
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Route::get('redirect-back-to-ticketing-portal', function() {
            $signer       = app(SignerInterfaceImplementation::class);
            $api_token    = config('ticketing-portal-client.apiToken');
            $project_id   = config('ticketing-portal-client.projectId');
            $redirect_url = Request::get('redirect_url');

            $sign_request = new SignRequest($api_token);
            if (!$sign_request->validateHash(Request::all())) {
                return App::abort(401);
            }

            if ($signer != null) {
                $url_generator = new UrlGenerator($signer, $api_token, $project_id);
                return Redirect::away($url_generator->getUrl($redirect_url));
            } else {
                //
            }
        })->middleware(['web', 'auth']);

    }
}
