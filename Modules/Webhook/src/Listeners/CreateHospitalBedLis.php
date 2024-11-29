<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Entities\BedType;
use Workdo\HospitalManagement\Entities\HospitalBed;
use Workdo\HospitalManagement\Entities\Patient;
use Workdo\HospitalManagement\Entities\Ward;
use Workdo\HospitalManagement\Events\CreateHospitalBed;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHospitalBedLis
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
    public function handle(CreateHospitalBed $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $hospitalbed = $event->hospitalbed;

            $bed_type = BedType::find($hospitalbed->bed_type);
            $ward = Ward::find($hospitalbed->ward_id);
            $patient = Patient::find($hospitalbed->patient_id);

            $web_array = [
                'Patient Name' => $patient->name,
                'Patient Email' => $patient->email,
                'Patient Mobile Number' => $patient->contact_no,
                'Bed Number' => $hospitalbed->bed_number,
                'Bed Type Title' => $bed_type->name,
                'Ward' => $ward->name,
                'Admission Date' => $hospitalbed->admission_date,
                'Discharge Date' => $hospitalbed->discharge_date,
                'Charges' => $hospitalbed->charges,
                'Status' => $hospitalbed->status,
                'comments' => $hospitalbed->comment
            ];

            $action = 'New Hospital Bed';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
