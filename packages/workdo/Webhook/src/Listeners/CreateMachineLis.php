<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MachineRepairManagement\Events\CreateMachine;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMachineLis
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
    public function handle(CreateMachine $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $machine = $event->machine;

            $web_array = [
                'Machine Title' => $machine->name,
                'Machine Manufacturer' => $machine->manufacturer,
                'Machine Model' => $machine->model,
                'Machine Installation Date' => $machine->installation_date,
                'Machine Description' => $machine->description,
                'Machine Last Maintenance Date' => $machine->last_maintenance_date,
                'Machine Status' => $machine->status,
            ];

            $action = 'New Machine';
            $module = 'MachineRepairManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
