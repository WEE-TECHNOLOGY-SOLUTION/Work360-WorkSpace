<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Entities\Doctor;
use Workdo\HospitalManagement\Entities\Patient;
use Workdo\HospitalManagement\Events\CreateHospitalAppointment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHospitalAppointmentLis
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
    public function handle(CreateHospitalAppointment $event)
    {
        if (module_is_active('Webhook')) {
            $hospitalappointment = $event->hospitalappointment;

            $patient = Patient::find($hospitalappointment->patient_id);
            $doctor = Doctor::find($hospitalappointment->doctor_id);

            $web_array = [
                'Patient Name' => $patient->name,
                'Patient Email' => $patient->email,
                'Patient Contact Number' => $patient->contact_no,
                'Doctor Name' => $doctor->name,
                'Doctor Email' => $doctor->email,
                'Doctor Contact Number' => $doctor->contact_no,
                'Appointment Date' => $hospitalappointment->date,
                'Appointment Start Time' => $hospitalappointment->start_time,
                'Appointment End Time' => $hospitalappointment->end_time,
            ];
            $action = 'New Hospital Appointment';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
