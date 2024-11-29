<?php

namespace Workdo\Mollie\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\Mollie\Providers\EventServiceProvider;
use Workdo\Mollie\Providers\RouteServiceProvider;
use Workdo\Mollie\Providers\CoursePayment;
use Workdo\Mollie\Providers\HotelRoomBookingPayment;
use Workdo\Mollie\Providers\InvoicePayment;
use Workdo\Mollie\Providers\RetainerPayment;
use Workdo\Mollie\Providers\ViewComposer;

class MollieServiceProvider extends ServiceProvider
{

    protected $moduleName = 'Mollie';
    protected $moduleNameLower = 'mollie';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(CoursePayment::class);
        $this->app->register(HotelRoomBookingPayment::class);
        $this->app->register(InvoicePayment::class);
        $this->app->register(RetainerPayment::class);
        $this->app->register(ViewComposer::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mollie');
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