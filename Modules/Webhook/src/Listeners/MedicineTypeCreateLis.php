<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PharmacyManagement\Entities\MedicineCategory;
use Workdo\PharmacyManagement\Events\MedicineTypeCreate;
use Workdo\Webhook\Entities\SendWebhook;

class MedicineTypeCreateLis
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
    public function handle(MedicineTypeCreate $event)
    {
        if (module_is_active('Webhook')) {
            $medicineType = $event->medicineType;
            $medicine_category = MedicineCategory::find($medicineType->medicine_category);

            $web_array = [
                'Medicine Type Title' => $medicineType->name,
                'Medicine Type Category' => $medicine_category->name,
                'Medicine Type Description' => $medicineType->description
            ];

            $action = 'New Medicine Type';
            $module = 'PharmacyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
