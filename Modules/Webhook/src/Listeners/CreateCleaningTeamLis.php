<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CleaningManagement\Events\CreateCleaningTeam;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCleaningTeamLis
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
    public function handle(CreateCleaningTeam $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $cleaning_team = $event->cleaning_team;

            $user_id = explode(',', $cleaning_team->user_id);

            $member_data = User::whereIn('id', $user_id)->get();

            $memberArray = [];

            foreach ($member_data as $member) {
                $attributes = [
                    'Member Name' => $member->name,
                    'Member Email' => $member->email,
                    'Member Mobile Number' => $member->mobile_no
                ];
                $memberArray[] = $attributes;
            }


            $web_array = [
                'Cleaning Team Name' => $cleaning_team->name,
                'Cleaning Team Members' => $memberArray,
                'Cleaning Team Status' => $cleaning_team->status == 1 ? 'Active' : 'Inactive',
            ];

            $action = 'New Cleaning Team';
            $module = 'CleaningManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
