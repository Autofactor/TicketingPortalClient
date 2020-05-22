<?php

namespace Autofactor\TicketingPortalClient\Middleware;

use Autofactor\TicketingPortalClient\SignRequest;
use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Session\Middleware\StartSession;

class SignRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('sign_token')) {
            $apiToken     = config('ticketing-portal-client.apiToken');
            $sign_request = new SignRequest($apiToken);
            if ($sign_request->validateHash($request->all())) {
                $request->session()->put('redirect_url',$request->get('redirect_url'));
                \Session::put('redirect_url',$request->get('redirect_url'));
                \Session::save();
            }
        }

        $response = $next($request);
        return $response;

    }

    public function terminate($request, $response)
    {
        
    }
}
