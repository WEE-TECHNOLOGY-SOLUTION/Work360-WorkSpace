<?php

namespace  Workdo\PayHere\Providers;

use App\Models\Invoice;
use Illuminate\Support\ServiceProvider;

class InvoicePayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

     public function boot(){
        view()->composer(['invoice.invoicepay'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route =='pay.invoice')
            {
                try {
                    $ids = \Request::segment(3);
                    if(!empty($ids))
                    {
                        $id = \Illuminate\Support\Facades\Crypt::decrypt($ids);
                        $invoice = Invoice::where('id',$id)->first();
                        $type = 'invoice';
                        $company_settings = getCompanyAllSetting( $invoice->created_by,$invoice->workspace);

                        if(module_is_active('PayHere', $invoice->created_by) && (isset($company_settings['payhere_is_on']) ? $company_settings['payhere_is_on'] : 'off') == 'on' && !empty($company_settings['payhere_merchant_id']) && !empty($company_settings['payhere_merchant_secret']) && !empty($company_settings['payhere_app_id']) && !empty($company_settings['payhere_app_secret']))
                        {
                            $view->getFactory()->startPush('invoice_payment_tab', view('pay-here::payment.sidebar'));
                            $view->getFactory()->startPush('invoice_payment_div', view('pay-here::payment.nav_containt_div',compact('type','invoice','company_settings')));
                        }
                    }
                } catch (\Throwable $th) {

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
