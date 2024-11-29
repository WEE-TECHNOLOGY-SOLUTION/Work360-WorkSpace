<?php

namespace Workdo\Mollie\Providers;

use Illuminate\Support\ServiceProvider;

class CoursePayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['lms::storefront.*.checkout'], function ($view)
        {
            try {
                $ids = \Request::segment(1);
                if(!empty($ids))
                {
                    try {
                        $store = \Workdo\LMS\Entities\Store::where('slug',$ids)->first();
                        $company_settings = getCompanyAllSetting($store->created_by,$store->workspace_id);
                        if(module_is_active('Mollie', $store->created_by) && ((isset($company_settings['mollie_payment_is_on']) ? $company_settings['mollie_payment_is_on'] : 'off' ) == 'on') && !empty($company_settings['company_mollie_api_key']) && isset($company_settings['company_mollie_profile_id']) && isset($company_settings['company_mollie_partner_id']))
                        {

                            $view->getFactory()->startPush('course_payment', view('mollie::payment.course_payment',compact('store')));
                        }
                    } catch (\Throwable $th)
                    {

                    }
                }
            } catch (\Throwable $th) {

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
