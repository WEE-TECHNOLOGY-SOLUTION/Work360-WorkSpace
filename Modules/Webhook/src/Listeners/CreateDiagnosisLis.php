<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MachineRepairManagement\Events\CreateDiagnosis;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDiagnosisLis
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
    public function handle(CreateDiagnosis $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $invoice = $event->invoice;

            $web_array = [
                'Customer Name' => $invoice->customer_name,
                'Customer Email' => $invoice->customer_email,
                'Invoice issue date' => $invoice->issue_date,
                'Invoice Due Date' => $invoice->due_date,
                'Estimated Time' => $invoice->estimated_time,
                'Service Charge' => $invoice->service_charge
            ];

            $action = 'New Diagnosis';
            $module = 'MachineRepairManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
