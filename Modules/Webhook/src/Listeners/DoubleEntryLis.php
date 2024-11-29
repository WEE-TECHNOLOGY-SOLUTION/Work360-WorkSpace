<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\DoubleEntry\Events\CreateJournalAccount;
use Workdo\Webhook\Entities\SendWebhook;

class DoubleEntryLis
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
    public function handle(CreateJournalAccount $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $journal = $event->journal;

            $accounts = $request->accounts;
            $accountIds = array_column($accounts, 'account');
            $accountNames = ChartOfAccount::whereIn('id', $accountIds)->pluck('name', 'id')->all();

            foreach ($accounts as &$entry) {
                $accountId = $entry['account'];

                if (isset($accountNames[$accountId])) {
                    $entry['account_name'] = $accountNames[$accountId];
                } else {

                    $entry['account_name'] = 'Unknown Account';
                }
            }

            $journal['account_data'] = $accounts;

            $action = 'New Journal Entry';
            $module = 'DoubleEntry';
            SendWebhook::SendWebhookCall($module, $journal, $action);
        }
    }
}
