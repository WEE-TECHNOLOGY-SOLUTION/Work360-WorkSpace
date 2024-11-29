<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperInvoiceLis
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
    public function handle(CreateNewspaperInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $invoice = $event->invoice;

            $agent = User::find($invoice->user_id);

            $web_array = [
                'Agent Name' => $agent->name,
                'Agent Email' => $agent->email,
                'Agent Mobile Number' => $agent->mobile_no,
                'Invoice Issue Date' => $invoice->issue_date,
                'Invoice Due Date' => $invoice->due_date,
                'Invoice Items' => $request->items
            ];

            $action = 'New Newspaper Invoice';
            $module = 'Newspaper';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
