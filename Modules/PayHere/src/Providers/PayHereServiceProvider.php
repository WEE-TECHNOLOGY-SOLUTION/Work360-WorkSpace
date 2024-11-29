<?php

namespace  Workdo\PayHere\Providers;

use Illuminate\Support\ServiceProvider;
use  Workdo\PayHere\Providers\EventServiceProvider;
use  Workdo\PayHere\Providers\RouteServiceProvider;
use  Workdo\PayHere\Providers\InvoicePayment;
use  Workdo\PayHere\Providers\RetainerPayment;
use  Workdo\PayHere\Providers\ViewComposer;

class PayHereServiceProvider extends ServiceProvider
{

    protected $moduleName = 'PayHere';
    protected $moduleNameLower = 'payhere';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(InvoicePayment::class);
        $this->app->register(RetainerPayment::class);
        $this->app->register(ViewComposer::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'pay-here');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerTranslations();
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(__DIR__.'/../Resources/lang');
        }
    }
}
