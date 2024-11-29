<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InnovationCenter\Entities\CreativityCategories;
use Workdo\InnovationCenter\Events\CreateChallenge;
use Workdo\Webhook\Entities\SendWebhook;

class CreateChallengeLis
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
    public function handle(CreateChallenge $event)
    {
        if (module_is_active('Webhook')) {
            $Challenges = $event->Challenges;

            $category = CreativityCategories::find($Challenges->category);

            $web_array = [
                'Challenge Title' => $Challenges->name,
                'Challenge Category Title' => $category->title,
                'Challenge End Date' => $Challenges->end_date,
                'Position' => $Challenges->position,
                'Explanation' => $Challenges->explantion,
                'Notes' => $Challenges->notes
            ];

            $action = 'New Challenges';
            $module = 'InnovationCenter';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
