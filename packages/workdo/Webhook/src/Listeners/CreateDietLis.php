<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Events\CreateDiet;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDietLis
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
    public function handle(CreateDiet $event)
    {
        if (module_is_active('Webhook')) {
            $diet = $event->diet;

            $web_array = [
                'Diet Title' => $diet->name
            ];

            $action = 'New Diet';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
