<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Events\CreateInquiry;
use Workdo\Webhook\Entities\SendWebhook;

class CreateInquiryLis
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
    public function handle(CreateInquiry $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $inquiry = $event->inquiry;

            $web_array = [
                'Child First Name' => $inquiry->child_first_name,
                'Child Last Name' => $inquiry->child_last_name,
                'Parent First Name' => $inquiry->parent_first_name,
                'Parent Last Name' => $inquiry->parent_last_name,
                'Parent Contact Number' => $inquiry->contact_number,
                'Child Age' => $inquiry->child_age,
                'Child Date of Birth' => $inquiry->child_dob,
                'Child Gender' => $inquiry->child_gender,
                'Inquiry Date' => $inquiry->date,
                'Message' => $inquiry->message,
            ];

            $action = 'New Inquiry';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
