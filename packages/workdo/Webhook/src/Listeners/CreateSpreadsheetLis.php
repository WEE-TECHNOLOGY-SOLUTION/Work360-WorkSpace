<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Spreadsheet\Events\CreateSpreadsheet;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSpreadsheetLis
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
    public function handle(CreateSpreadsheet $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $spreadsheets = $event->spreadsheets;

            $user = User::find($spreadsheets->user_id);
            $spreadsheets->user_name = $user->name;

            $action = 'New Spreadsheet';
            $module = 'Spreadsheet';
            SendWebhook::SendWebhookCall($module, $spreadsheets, $action);
        }
    }
}
