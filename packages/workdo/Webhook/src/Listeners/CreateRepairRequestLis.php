<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MachineRepairManagement\Entities\Machine;
use Workdo\MachineRepairManagement\Events\CreateRepairRequest;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRepairRequestLis
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
    public function handle(CreateRepairRequest $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $repair_request = $event->repair_request;

            $machine = Machine::find($repair_request->machine_id);

            $web_array = [
                'Customer Name' => $repair_request->customer_name,
                'Customer Email' => $repair_request->customer_email,
                'Machine Title' => $machine->name,
                'Machine Manufacturer' => $machine->manufacturer,
                'Machine Model' => $machine->model,
                'Machine Installation Date' => $machine->installation_date,
                'Machine Last Maintenance Date' => $machine->last_maintenance_date,
                'Repair Priority Level' => $repair_request->priority_level,
                'Description of Issue' => $repair_request->description_of_issue,
            ];

            $action = 'New Repair Request';
            $module = 'MachineRepairManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
