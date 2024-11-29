<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Events\CreateSkill;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSkillLis
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
    public function handle(CreateSkill $event)
    {
        if (module_is_active('Webhook')) {
            $skill = $event->skill;

            $web_array = [
                'Skill' => $skill->name
            ];

            $action = 'New Skill';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
