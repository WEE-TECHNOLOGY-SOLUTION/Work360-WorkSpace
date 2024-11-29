<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MobileServiceManagement\Events\MobileServiceCreate;
use Workdo\Webhook\Entities\SendWebhook;

class MobileServiceCreateLis
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
    public function handle(MobileServiceCreate $event)
    {
        if (module_is_active('Webhook')) {
            $data = $event->data;

            $web_array = [
                'Service ID' => $data->service_id,
                'Customer Name' => $data->customer_name,
                'Mobile Number' => $data->mobile_no,
                'Email' => $data->email,
                'Priority' => $data->priority,
                'Mobile Name' => $data->mobile_name,
                'Mobile Company' => $data->mobile_company,
                'Mobile Model' => $data->mobile_model,
                'Description' => $data->description,
                'Tracking Status' => $data->tracking_status
            ];

            $action = 'New Mobile Service';
            $module = 'MobileServiceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
