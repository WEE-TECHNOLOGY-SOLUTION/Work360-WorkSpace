<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Entities\Childcare;
use Workdo\ChildcareManagement\Entities\Parents;
use Workdo\ChildcareManagement\Events\CreateChild;
use Workdo\Webhook\Entities\SendWebhook;

class CreateChildLis
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
    public function handle(CreateChild $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $child = $event->child;

            $parent = Parents::find($child->parent_id);
            $childcare = Childcare::find($child->childcare_id);

            $web_array = [
                'Child Care Title' => $childcare->name,
                'Child Care Address' => $childcare->address,
                'Child Care Contact Number' => $childcare->contact_number,
                'First Name' => $child->first_name,
                'Last Name' => $child->last_name,
                'Date of Birth' => $child->dob,
                'Gender' => $child->gender,
                'Age' => $child->age,
                'Parent First Name' => $parent->first_name,
                'Parent Last Name' => $parent->last_name,
                'Parent Email' => $parent->email,
                'Parent Contact Number' => $parent->contact_number,
                'Parent Address' => $parent->address
            ];

            $action = 'New Child';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
