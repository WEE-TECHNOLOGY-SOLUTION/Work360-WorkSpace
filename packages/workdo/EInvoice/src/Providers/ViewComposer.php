<?php

namespace Workdo\EInvoice\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    public function boot(){
        view()->composer(['account::customer.create','account::customer.edit','account::customer.show'], function ($view)
        {
            if(\Auth::check())
            {
                $active_module = explode(',',\Auth::user()->active_module);
                $dependency = explode(',','EInvoice');
                $customer = $view->customer;
                if(!empty(array_intersect($dependency,$active_module)))
                {
                    $view->getFactory()->startPush('electronic_address', view('einvoice::customer.electronic_address'));
                    $view->getFactory()->startPush('show_electronic_address', view('einvoice::customer.show_electronic_address',compact('customer')));
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
