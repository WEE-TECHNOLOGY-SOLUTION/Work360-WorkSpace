<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PharmacyManagement\Events\MedicineCategoryCreate;
use Workdo\Webhook\Entities\SendWebhook;

class MedicineCategoryCreateLis
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
    public function handle(MedicineCategoryCreate $event)
    {
        if (module_is_active('Webhook')) {
            $MedicineCategory = $event->MedicineCategory;

            $web_array = [
                'Medicine Category Title' => $MedicineCategory->name
            ];

            $action = 'New Medicine Category';
            $module = 'PharmacyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
