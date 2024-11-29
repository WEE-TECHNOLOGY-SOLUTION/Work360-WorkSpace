<?php

namespace  Workdo\PayHere\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Facades\ModuleFacade as Module;
class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot(){
        view()->composer(['plans.marketplace','plans.planpayment'], function ($view)
        {
            if(Auth::check())
            {
                $admin_settings = getAdminAllSetting();
                if(Module::isEnabled('PayHere') && (isset($admin_settings['payhere_is_on']) ? $admin_settings['payhere_is_on'] : 'off') == 'on' && !empty($admin_settings['payhere_merchant_id']) && !empty($admin_settings['payhere_merchant_secret']) && !empty($admin_settings['payhere_app_id']) && !empty($admin_settings['payhere_app_secret']))
                {
                    $view->getFactory()->startPush('company_plan_payment', view('pay-here::payment.plan_payment'));
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
