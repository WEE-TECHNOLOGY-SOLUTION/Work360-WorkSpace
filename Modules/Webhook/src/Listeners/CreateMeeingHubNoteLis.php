<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MeetingHub\Events\CreateMeeingHubNote;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMeeingHubNoteLis
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
    public function handle(CreateMeeingHubNote $event)
    {
        if (module_is_active('Webhook')) {
            $notes = $event->notes;

            $user = User::find($notes->user_id);

            $web_array = [
                'User Name' => $user->name,
                'User Email' => $user->email,
                'User Mobile Number' => $user->mobile_no,
                'Note' => $notes->note
            ];

            $action = 'New Meeting Note';
            $module = 'MeetingHub';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
