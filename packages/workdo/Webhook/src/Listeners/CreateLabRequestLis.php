<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MedicalLabManagement\Entities\Patient;
use Workdo\MedicalLabManagement\Entities\TestContent;
use Workdo\MedicalLabManagement\Events\CreateLabRequest;
use Workdo\Webhook\Entities\SendWebhook;

class CreateLabRequestLis
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
    public function handle(CreateLabRequest $event)
    {
        if (module_is_active('Webhook')) {
            $labRequest = $event->labRequest;
            $request = $event->request;

            $patient = Patient::find($labRequest->patient_id);
            $test_content = TestContent::find($labRequest->content_id);

            $web_array = [
                'Patient First Name' => $patient->first_name,
                'Patient Last Name' => $patient->last_name,
                'Patient Date of Birth' => $patient->dob,
                'Priority' => $labRequest->priority,
                'Date' => $labRequest->date,
                'Test Content Title' => $test_content->name,
                'Test Content Code' => $test_content->code,
            ];

            $action = 'New Lab Request';
            $module = 'MedicalLabManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
