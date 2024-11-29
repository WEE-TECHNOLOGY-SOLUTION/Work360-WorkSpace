<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MedicalLabManagement\Events\CreateLabPatient;
use Workdo\Webhook\Entities\SendWebhook;

class CreateLabPatientLis
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
    public function handle(CreateLabPatient $event)
    {
        if (module_is_active('Webhook')) {
            $patient = $event->patient;

            $web_array = [
                'First Name' => $patient->first_name,
                'Last Name' => $patient->last_name,
                'Date Of Birth' => $patient->dob,
                'Patient Gender' => $patient->gender == 0 ? 'Male' : 'Female',
                'Patient Contact Number' => $patient->contact,
                'Patient Address' => $patient->address,
                'Patient Blood Group' => $patient->blood_group,
                'Patient Email' => $patient->email,
                'Insurance' => $patient->insurance
            ];

            $action = 'New Lab Patient';
            $module = 'MedicalLabManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
