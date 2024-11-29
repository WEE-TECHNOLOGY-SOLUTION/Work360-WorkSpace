<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Sage\Events\CreateLedgerAccount;
use Workdo\Webhook\Entities\SendWebhook;

class CreateLedgerAccountLis
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
    public function handle(CreateLedgerAccount $event)
    {
        if (module_is_active('Webhook')) {
            $sageLedgerAccount = $event->sageLedgerAccount;

            $web_array = [
                'Ledger Account Title' => $sageLedgerAccount->name
            ];

            $action = 'New Ledger Account';
            $module = 'Sage';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
