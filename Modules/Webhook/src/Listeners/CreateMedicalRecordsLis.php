<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Entities\Doctor;
use Workdo\HospitalManagement\Entities\Patient;
use Workdo\HospitalManagement\Events\CreateMedicalRecords;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMedicalRecordsLis
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
    public function handle(CreateMedicalRecords $event)
    {
        if (module_is_active('Webhook')) {
            $medicalrecord = $event->medicalrecord;

            $patient = Patient::find($medicalrecord->patient_id);
            $doctor = Doctor::find($medicalrecord->doctor_id);

            $web_array = [
                'Patient Name' => $patient->name,
                'Patient Email' => $patient->email,
                'Patient Contact Number' => $patient->contact_no,
                'Doctor Name' => $doctor->name,
                'Doctor Email' => $doctor->email,
                'Doctor Contact Number' => $doctor->contact_no,
                'Date' => $medicalrecord->date,
                'Symptoms' => $medicalrecord->symptoms,
                'Follow Up Instructions' => $medicalrecord->follow_up_instructions,
                'Diagnosis' => $medicalrecord->diagnosis,
                'Prescription' => $medicalrecord->prescription,
                'Test Results' => $medicalrecord->testresults,
            ];

            $action = 'New Medical Records';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
