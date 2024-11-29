<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Events\CreateParent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateParentLis
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
    public function handle(CreateParent $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $parent = $event->parent;

            $web_array = [
                'Parent First Name' => $parent->first_name,
                'Parent Last Name' => $parent->last_name,
                'Email' => $parent->email,
                'Contact Number' => $parent->contact_number,
                'Parent Address' => $parent->address,
            ];

            $action = 'New Parent';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
