<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Entities\Cases;
use Workdo\LegalCaseManagement\Events\CreateFeeRecieve;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFeeRecieveLis
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
    public function handle(CreateFeeRecieve $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $feeReceive = $event->feeReceive;

            $case = Cases::find($feeReceive->case_id);
            $member = User::find($feeReceive->member);

            $web_array = [
                'Case Title' => $case->title,
                'Date' => $feeReceive->date,
                'Particulars' => $feeReceive->particulars,
                'Money' => $feeReceive->money,
                'Method' => $feeReceive->method,
                'Notes' => $feeReceive->notes,
                'Member' => $member->name
            ];

            $action = 'New Fee Recieve';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
