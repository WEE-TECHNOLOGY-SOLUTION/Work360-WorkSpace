<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Events\CreateMedicineCategory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMedicineCategoryLis
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
    public function handle(CreateMedicineCategory $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $medicinecategory = $event->medicinecategory;

            $web_array = [
                'Medicine Category' => $medicinecategory->name
            ];

            $action = 'New Ward';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
