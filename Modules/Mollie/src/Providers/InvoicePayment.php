<?php

namespace Workdo\Mollie\Providers;

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
            if($route == "pay.invoice")
            {
                try {
                    $ids = \Request::segment(3);
                    if(!empty($ids))
                    {
                        try {
                            $id = \Illuminate\Support\Facades\Crypt::decrypt($ids);
                            $invoice = Invoice::where('id',$id)->first();
                            $type = 'invoice';
                            $company_settings = getCompanyAllSetting($invoice->created_by,$invoice->workspace_id);
                            if(module_is_active('Mollie', $invoice->created_by) && ((isset($company_settings['mollie_payment_is_on']) ? $company_settings['mollie_payment_is_on'] : 'off' ) == 'on') && !empty($company_settings['company_mollie_api_key']) && isset($company_settings['company_mollie_profile_id']) && isset($company_settings['company_mollie_partner_id']))
                            {
                                $view->getFactory()->startPush('invoice_payment_tab', view('mollie::payment.sidebar'));
                                $view->getFactory()->startPush('invoice_payment_div', view('mollie::payment.nav_containt_div',compact('type','invoice','company_settings')));
                            }
                        } catch (\Throwable $th)
                        {

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