<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Entities\Doctor;
use Workdo\HospitalManagement\Entities\Specialization;
use Workdo\HospitalManagement\Events\CreatePatient;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePatientLis
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
    public function handle(CreatePatient $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $patient = $event->patient;

            $doctor = Doctor::find($patient->doctor_id);
            $specialization = Specialization::find($doctor->specialization);

            $web_array = [
                'Patient Name' => $patient->name,
                'Patient Email' => $patient->email,
                'Patient Date of Birth' => $patient->dob,
                'Patient Gender' => $patient->gender,
                'Patient Contact Number' => $patient->contact_no,
                'Patient Medical History' => $patient->medical_history,
                'Patient Address' => $patient->address,
                'Doctor Name' => $doctor->name,
                'Doctor Email' => $doctor->email,
                'Doctor Contact Number' => $doctor->contact_no,
                'Doctor Specialization' => $specialization->name
            ];

            $action = 'New Patient';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
