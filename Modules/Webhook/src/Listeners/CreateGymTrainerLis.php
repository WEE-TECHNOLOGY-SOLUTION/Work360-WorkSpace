<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Entities\GymMember;
use Workdo\GymManagement\Entities\Skill;
use Workdo\GymManagement\Events\CreateGymTrainer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateGymTrainerLis
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
    public function handle(CreateGymTrainer $event)
    {
        if (module_is_active('Webhook')) {
            $gymtrainer = $event->gymtrainer;

            $skillIds = explode(',', $gymtrainer->skill_id);
            $memberIds = explode(',', $gymtrainer->member_id);

            $skillsData = Skill::whereIn('id', $skillIds)->get();
            $membersData = GymMember::whereIn('id', $memberIds)->get();

            $skillsArray = $skillsData->pluck(['name'])->toArray();
            $membersArray = $membersData->map(function ($member) {
                return [
                    'name' => $member['name'],
                    'email' => $member['email'],
                ];
            })->toArray();


            $web_array = [
                'Trainer Name' => $gymtrainer->name,
                'Trainer Email' => $gymtrainer->email,
                'Skills' => $skillsArray,
                'Members' => $membersArray,
                'Country' => $gymtrainer->country,
                'State' => $gymtrainer->state,
                'City' => $gymtrainer->city,
                'Contact' => $gymtrainer->contact,
                'Zip' => $gymtrainer->zip,
                'Address' => $gymtrainer->address,
            ];

            $action = 'New GYM Trainer';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
