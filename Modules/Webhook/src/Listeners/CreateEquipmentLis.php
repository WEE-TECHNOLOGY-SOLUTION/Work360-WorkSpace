<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Events\CreateEquipment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateEquipmentLis
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
    public function handle(CreateEquipment $event)
    {
        if (module_is_active('Webhook')) {
            $equipment = $event->equipment;

            $web_array = [
                'Equipment' => $equipment->name
            ];

            $action = 'New Equipment';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
