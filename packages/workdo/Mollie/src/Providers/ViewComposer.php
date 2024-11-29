<?php

namespace Workdo\Mollie\Providers;

use App\Facades\ModuleFacade as Module;
use Illuminate\Support\ServiceProvider;

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
                if((Module::isEnabled('Mollie') && isset($admin_settings['mollie_payment_is_on']) ? $admin_settings['mollie_payment_is_on'] : 'off') == 'on' && !empty($admin_settings['company_mollie_api_key']) && !empty($admin_settings['company_mollie_profile_id']) && !empty($admin_settings['company_mollie_partner_id']))
                {
                    $view->getFactory()->startPush('company_plan_payment', view('mollie::payment.plan_payment'));
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
