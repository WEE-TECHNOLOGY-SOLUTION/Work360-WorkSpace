<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MedicalLabManagement\Entities\LabTest;
use Workdo\MedicalLabManagement\Entities\Patient;
use Workdo\MedicalLabManagement\Events\CreateMedicalAppoinment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMedicalAppoinmentLis
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
    public function handle(CreateMedicalAppoinment $event)
    {
        if (module_is_active('Webhook')) {
            $labAppoinment = $event->labAppoinment;

            $patient = Patient::find($labAppoinment->patient_id);
            $lab = LabTest::find($labAppoinment->lab_id);

            $web_array = [
                'Patient First Name' => $patient->first_name,
                'Patient Last Name' => $patient->last_name,
                'Patient Date of Birth' => $patient->dob,
                'Test Lab Title' => $lab->name,
                'Test Lab Cost' => $lab->cost,
                'Tests' => json_decode($lab->items),
                'Appointment Date' => $labAppoinment->date,
                'Appointment Time' => $labAppoinment->time,
            ];

            $action = 'New Medical Appointment';
            $module = 'MedicalLabManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
