<?php

namespace Workdo\Stripe\Providers;

use App\Models\WorkSpace;
use Illuminate\Support\ServiceProvider;

class MovieShowBookingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot(){

        view()->composer(['movie-show-booking-system::frontend.checkout'], function ($view)
        {
                $slug = request()->query('slug');

                $workspace = WorkSpace::where('slug',$slug)->first();

                $company_settings = getCompanyAllSetting($workspace->created_by,$workspace->id);
                if((isset($company_settings['stripe_is_on']) ? $company_settings['stripe_is_on'] : 'off') == 'on' && !empty($company_settings['stripe_key']) && !empty($company_settings['stripe_secret']))
                {
                    $view->getFactory()->startPush('movieshowbooking_payment', view('stripe::payment.movieshowbooking_payment',compact('slug')));
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
