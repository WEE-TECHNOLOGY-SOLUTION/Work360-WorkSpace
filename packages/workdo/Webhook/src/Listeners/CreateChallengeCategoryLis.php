<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InnovationCenter\Events\CreateCategory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateChallengeCategoryLis
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
    public function handle(CreateCategory $event)
    {
        if (module_is_active('Webhook')) {
            $CreativityCategories = $event->CreativityCategories;

            $web_array = [
                'Creative Categories' => $CreativityCategories->title,
            ];

            $action = 'New Challenge Category';
            $module = 'InnovationCenter';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
