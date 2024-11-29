<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Sales\Events\CreateMeeting;

class CreateMeetingLis
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
    public function handle(CreateMeeting $event)
    {
        if(module_is_active('Webhook'))
        {
            $request = $event->request;
            $meeting = $event->meeting;
            $user = User::where('id',$request->user)->first();
            $attendees_user = User::where('id',$request->attendees_user)->first();
            $attendees_contact = \Workdo\Sales\Entities\Contact::where('id',$request->attendees_contact)->first();
            $attendees_lead =  \Workdo\Lead\Entities\Lead::where('id',$request->attendees_lead)->first();
            $meeting->user_name             = !empty($user) ? $user->name : '';
            $meeting->status              = !empty($meeting) ? $meeting->status : '';
            $meeting->parent_name           = $meeting->parent_name;
            $meeting->attendees_user      = !empty($attendees_user) ? $attendees_user->name:'';
            $meeting->attendees_contact   = !empty($attendees_contact) ? $attendees_contact->name:'';
            $meeting->attendees_lead      = !empty($attendees_lead) ? $attendees_lead->name:'';
            $action = 'New Meeting';
            $module = 'Sales';
            SendWebhook::SendWebhookCall($module ,$meeting,$action);
        }
    }
}
