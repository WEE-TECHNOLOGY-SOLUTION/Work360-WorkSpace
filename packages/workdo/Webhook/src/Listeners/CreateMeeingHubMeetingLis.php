<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Lead\Entities\Lead;
use Workdo\MeetingHub\Entities\MeetingHubModule;
use Workdo\MeetingHub\Entities\MeetingType;
use Workdo\MeetingHub\Events\CreateMeeingHubMeeting;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMeeingHubMeetingLis
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
    public function handle(CreateMeeingHubMeeting $event)
    {
        if (module_is_active('Webhook')) {
            $meeting = $event->meeting;

            $sub_module = MeetingHubModule::find($meeting->sub_module);
            $meeting_type = MeetingType::find($meeting->meeting_type);
            $web_array = [];

            if ($sub_module->module == 'Account') {
                if ($sub_module->submodule == 'Client') {

                    $client_id = explode(',', $meeting->user_id);

                    $clients = User::whereIn('id', $client_id)->get();

                    $client_data = [];

                    foreach ($clients as $client) {
                        $clientArray = [
                            'Client Name' => $client->name,
                            'Client Email' => $client->email,
                            'Client Mobile Number' => $client->mobile_no,
                        ];
                        $client_data[] = $clientArray;
                    }

                    $web_array = [
                        'Caller' => $meeting->caller,
                        'Meeting Subject' => $meeting->subject,
                        'Meeting Type' => $meeting_type->name,
                        'Meeting Module' => $sub_module->module,
                        'Meeting Sub Module' => $sub_module->submodule,
                        'Meeting Description' => $meeting->description,
                        'Meeting Location' => $meeting->location,
                        'Meeting Clients' => $client_data
                    ];
                } else if ($sub_module->submodule == 'Vendor') {
                    $vendor_id = explode(',', $meeting->user_id);

                    $vendors = User::whereIn('id', $vendor_id)->get();

                    $vendor_data = [];

                    foreach ($vendors as $vendor) {
                        $vendorArray = [
                            'Vendor Name' => $vendor->name,
                            'Vendor Email' => $vendor->email,
                            'Vendor Mobile Number' => $vendor->mobile_no,
                        ];
                        $vendor_data[] = $vendorArray;
                    }

                    $web_array = [
                        'Caller' => $meeting->caller,
                        'Meeting Subject' => $meeting->subject,
                        'Meeting Type' => $meeting_type->name,
                        'Meeting Module' => $sub_module->module,
                        'Meeting Sub Module' => $sub_module->submodule,
                        'Meeting Description' => $meeting->description,
                        'Meeting Location' => $meeting->location,
                        'Meeting Vendors' => $vendor_data
                    ];
                }
            }

            if ($sub_module->module == 'Lead' && $sub_module->submodule == 'Lead') {
                $user = User::find($meeting->user_id);
                $lead = Lead::find($meeting->lead_id);

                $web_array = [
                    'Caller' => $meeting->caller,
                    'Meeting Subject' => $meeting->subject,
                    'Meeting Type' => $meeting_type->name,
                    'Meeting Module' => $sub_module->module,
                    'Meeting Sub Module' => $sub_module->submodule,
                    'Meeting Description' => $meeting->description,
                    'Meeting Location' => $meeting->location,
                    'Meeting Lead' => $lead->name,
                    'Lead Email' => $lead->email,
                    'Lead Subject' => $lead->subject,
                    'User Name' => $user->name,
                    'User Email' => $user->email,
                    'User Mobile Number' => $user->mobile_no,
                ];
            }

            if ($sub_module->module == 'Hrm' && $sub_module->submodule == 'Employee') {
                $employee_id = explode(',', $meeting->user_id);

                $employees = User::whereIn('id', $employee_id)->get();

                $employee_data = [];

                foreach ($employees as $employee) {
                    $employeeArray = [
                        'Employee Name' => $employee->name,
                        'Employee Email' => $employee->email,
                        'Employee Mobile Number' => $employee->mobile_no,
                    ];
                    $employee_data[] = $employeeArray;
                }

                $web_array = [
                    'Caller' => $meeting->caller,
                    'Meeting Subject' => $meeting->subject,
                    'Meeting Type' => $meeting_type->name,
                    'Meeting Module' => $sub_module->module,
                    'Meeting Sub Module' => $sub_module->submodule,
                    'Meeting Description' => $meeting->description,
                    'Meeting Location' => $meeting->location,
                    'Employees' => $employee_data,
                ];
            }

            $action = 'New Meeting';
            $module = 'MeetingHub';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
