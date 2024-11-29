<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Events\CreateBodyPart;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBodyPartLis
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
    public function handle(CreateBodyPart $event)
    {
        if (module_is_active('Webhook')) {
            $bodypart = $event->bodypart;

            $web_array = [
                'Body Part Title' => $bodypart->name
            ];

            $action = 'New Body Part';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
