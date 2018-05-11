<?php

namespace Mid\OutterGameConfigTicket;

use Illuminate\Support\ServiceProvider;

class OutterGameConfigTicketServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $namespace = 'Mid\OutterGameConfigTicket';
        $config_folder = "OutterGameConfigTicket";//config存放目錄
        $this->loadTranslationsFrom(__DIR__ . '/lang', $namespace);
        $this->loadViewsFrom(__DIR__ . '/views', $namespace);
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->publishes(
            [__DIR__ . '/config' => config_path($config_folder)], $namespace
        );
        $this->constantBoot();
    }

    private function constantBoot()
    {
        $patten = __DIR__ . '/constant/*.php';
        $files = glob($patten, GLOB_BRACE);
        foreach ($files as $file) {
            require $file;
        }
    }
}
