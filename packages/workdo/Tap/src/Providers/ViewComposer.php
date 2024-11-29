<?php

namespace Workdo\tap\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\ModuleFacade as Module;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    public function boot()
    {
        view()->composer(['plans.marketplace','plans.planpayment'], function ($view)
        {
            if(\Auth::check())
            {
                $admin_settings = getAdminAllSetting();

                if(Module::isEnabled('Tap') && isset($admin_settings['tap_payment_is_on']) && $admin_settings['tap_payment_is_on'] == 'on' && !empty($admin_settings['company_tap_secret_key']))
                {
                    $view->getFactory()->startPush('company_plan_payment', view('tap::payment.plan_payment'));
                }
            }
        });
    }
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
