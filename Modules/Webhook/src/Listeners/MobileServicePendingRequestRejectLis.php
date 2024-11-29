<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MobileServiceManagement\Events\MobileServicePendingRequestReject;
use Workdo\Webhook\Entities\SendWebhook;

class MobileServicePendingRequestRejectLis
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
    public function handle(MobileServicePendingRequestReject $event)
    {
        if (module_is_active('Webhook')) {
            $findService = $event->findService;

            $web_array = [
                'Service ID' => $findService->service_id,
                'Customer Name' => $findService->customer_name,
                'Customer Email' => $findService->email,
                'Customer Mobile Number' => $findService->mobile_no,
                'Priority' => $findService->priority,
                'Mobile Name' => $findService->mobile_name,
                'Mobile Company' => $findService->mobile_company,
                'Mobile Model' => $findService->mobile_model,
                'Description' => $findService->description,
                'Service Status' => $findService->is_approve == 0 ? 'Rejected' : '',
            ];

            $action = 'New Mobile Service Request Reject';
            $module = 'MobileServiceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
