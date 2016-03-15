<?php

namespace Albertarni\TicketingPortalClient;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->getMyConfigPath() => config_path(self::getMyConfigName().'.php'),
            ], 'config');
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getMyConfigPath(), self::getMyConfigName()
        );
    }
    private function getMyConfigPath()
    {
        return __DIR__.'/config.php';
    }
    private static function getMyConfigName()
    {
        return 'ticketing-portal';
    }
    public static function config($key)
    {
        return Config::get(self::getMyConfigName().'.'.$key);
    }
}
