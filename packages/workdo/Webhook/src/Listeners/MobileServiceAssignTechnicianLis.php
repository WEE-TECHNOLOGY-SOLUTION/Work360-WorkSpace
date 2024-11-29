<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MobileServiceManagement\Events\MobileServiceAssignTechnician;
use Workdo\Webhook\Entities\SendWebhook;

class MobileServiceAssignTechnicianLis
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
    public function handle(MobileServiceAssignTechnician $event)
    {
        if (module_is_active('Webhook')) {
            $technicianData = $event->technicianData;
            $mobileServiceReq = $event->mobileServiceReq;

            $technician = User::find($technicianData['technician_id']);

            $web_array = [
                'Service ID' => $mobileServiceReq->service_id,
                'Customer Name' => $mobileServiceReq->customer_name,
                'Customer Email' => $mobileServiceReq->email,
                'Priority' => $mobileServiceReq->priority,
                'Mobile Name' => $mobileServiceReq->mobile_name,
                'Mobile Company' => $mobileServiceReq->mobile_company,
                'Mobile Model' => $mobileServiceReq->mobile_model,
                'Technician Name' => $technician->name,
                'Technician Email' => $technician->email
            ];

            $action = 'New Mobile Service Technician Assign';
            $module = 'MobileServiceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
