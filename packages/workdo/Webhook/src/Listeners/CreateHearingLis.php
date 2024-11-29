<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Entities\Cases;
use Workdo\LegalCaseManagement\Entities\Court;
use Workdo\LegalCaseManagement\Entities\HighCourt;
use Workdo\LegalCaseManagement\Events\CreateHearing;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHearingLis
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
    public function handle(CreateHearing $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $hearing = $event->hearing;

            $case = Cases::find($hearing->case_id);
            $court = Court::find($case->court_id);

            $web_array = [
                'Case Number' => $case->case_number,
                'Case Year' => $case->year,
                'Case Title' => $case->title,
                'Hearing Date' => $hearing->date,
                'Court' => $court->name,
                'Court Type' => $court->type,
                'Court Location' => $court->location,
                'Court Address' => $court->address,
                'Remarks' => $hearing->remark,
            ];

            $action = 'New Hearing';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
