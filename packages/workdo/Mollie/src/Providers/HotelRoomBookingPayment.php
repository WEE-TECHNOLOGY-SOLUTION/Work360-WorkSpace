<?php

namespace Workdo\Mollie\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\Holidayz\Entities\Hotels;

class HotelRoomBookingPayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot(){
        view()->composer(['holidayz::frontend.*.checkout'], function ($view) //try * replace to theme1
        {
            try {
                $slug = \Request::segment(2);
                if(!empty($slug))
                {
                    $hotel = Hotels::where('slug',$slug)->where('is_active', '1')->first();
                    $company_settings = getCompanyAllSetting($hotel->created_by,$hotel->workspace_id);
                    if(module_is_active('Mollie', $hotel->created_by) && ((isset($company_settings['mollie_payment_is_on']) ? $company_settings['mollie_payment_is_on'] : 'off' ) == 'on') && !empty($company_settings['company_mollie_api_key']) && isset($company_settings['company_mollie_profile_id']) && isset($company_settings['company_mollie_partner_id']))
                    {
                        $view->getFactory()->startPush('hotel_room_booking_payment_div', view('mollie::payment.holidayz_nav_containt_div',compact('slug')));

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
