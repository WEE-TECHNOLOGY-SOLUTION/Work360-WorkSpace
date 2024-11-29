<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Lead\Events\LeadConvertDeal;
use Workdo\Webhook\Entities\SendWebhook;

class LeadConvertDealLis
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
    public function handle(LeadConvertDeal $event)
    {
        if (module_is_active('Webhook')) {
            $lead = $event->lead;
            $request = $event->request;

            $user = User::find($lead->user_id);

            $web_array = [
                'Deal Name' => $request->name,
                'Deal Price' => $request->price,
                'Client Name' => $request->client_name,
                'Client Email' => $request->client_email,
                'Subject' => $lead->subject,
                'User Name' => $user->name,
                'User Email' => $user->email,
            ];

            $action = 'Convert To Deal';
            $module = 'Lead';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
