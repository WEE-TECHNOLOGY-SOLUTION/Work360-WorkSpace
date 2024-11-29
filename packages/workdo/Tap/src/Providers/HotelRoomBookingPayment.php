<?php

namespace Workdo\Tap\Providers;

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
        view()->composer(['holidayz::frontend.*.checkout'], function ($view)
        {
            try {
                $slug = \Request::segment(2);
                if(!empty($slug))
                {
                    $hotel = Hotels::where('slug',$slug)->where('is_active', '1')->first();
                    $company_settings = getCompanyAllSetting($hotel->created_by,$hotel->workspace);
                    if(module_is_active('Tap', $hotel->created_by) && ($company_settings['tap_payment_is_on']  == 'on') && ($company_settings['company_tap_secret_key']))
                    {
                        $view->getFactory()->startPush('hotel_room_booking_payment_div', view('tap::payment.holidayz_nav_containt_div',compact('slug')));
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
