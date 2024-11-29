<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Entities\Insurance;
use Workdo\InsuranceManagement\Entities\Policy;
use Workdo\InsuranceManagement\Events\CreateinsuranceInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CreateinsuranceInvoiceLis
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
    public function handle(CreateinsuranceInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $invoice = $event->invoice;

            $policy = Policy::find($invoice->policy_id);
            $client = User::find($invoice->client_id);

            $web_array = [
                'Policy Name' => $policy->name,
                'Policy Type' => $policy->type,
                'Policy Duration' => $policy->duration,
                'Policy Amount' => $policy->amount,
                'Client Name' => $client->name,
                'Client Email' => $client->email,
                'Due Date' => $invoice->due_date,
            ];

            $action = 'New Insurance Invoice';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
