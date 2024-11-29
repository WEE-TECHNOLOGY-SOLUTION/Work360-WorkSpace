<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Entities\Specialization;
use Workdo\HospitalManagement\Events\CreateDoctor;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDoctorLis
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
    public function handle(CreateDoctor $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $doctor = $event->doctor;

            $specialization = Specialization::find($doctor->specialization);

            $web_array = [
                'Doctor Name' => $doctor->name,
                'Doctor Specialization' => $specialization->name,
                'Doctor Contact Number' => $doctor->contact_no,
                'Doctor Email' => $doctor->email,
                'Doctor License Number' => $doctor->license_number,
                'Gender' => $doctor->gender,
                'Doctor Years of Experience' => $doctor->years_of_experience,
                'Doctor Consultation Fee' => $doctor->consultation_fee,
            ];

            $action = 'New Doctor';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
