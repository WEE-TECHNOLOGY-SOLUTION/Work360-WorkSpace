<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ToDo\Events\ToDoStageSystemSetup;
use Workdo\Webhook\Entities\SendWebhook;

class ToDoStageSystemSetupLis
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
    public function handle(ToDoStageSystemSetup $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;

            $namesArray = [];

            foreach ($request->stages as $item) {
                $namesArray[] = $item['name'];
            }

            $web_array = [
                'State Titles' => $namesArray
            ];

            $action = 'New To Do Stage';
            $module = 'ToDo';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
