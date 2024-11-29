<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Events\CreateAdvocate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAdvocateLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateAdvocate $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $advocate = $event->advocate;

            $web_array = [
                'Advocate Name' => $request->name,
                'Advocate Email' => $request->email,
                'Advocate Phone Number' => $request->phone_number,
                'Advocate Age' => $advocate->age,
                'Company Name' => $advocate->company_name,
                'Bank Details' => $advocate->bank_details,
                'Office Address Line 1' => $advocate->ofc_address_line_1,
                'Office Address Line 2' => $advocate->ofc_address_line_2,
                'Office Country' => $advocate->ofc_country,
                'Office State' => $advocate->ofc_state,
                'Office City' => $advocate->ofc_city,
                'Office Zip Code' => $advocate->ofc_zip_code,
                'Home Address Line 1' => $advocate->home_address_line_1,
                'Home Address Line 2' => $advocate->home_address_line_2,
                'Home Country' => $advocate->home_country,
                'Home State' => $advocate->home_state,
                'Home City' => $advocate->home_city,
                'Home Zip Code' => $advocate->home_zip_code,
            ];

            $action = 'New Advocate';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
