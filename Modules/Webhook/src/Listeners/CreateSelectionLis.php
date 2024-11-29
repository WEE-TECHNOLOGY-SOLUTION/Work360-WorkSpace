<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CateringManagement\Events\CreateSelection;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSelectionLis
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
    public function handle(CreateSelection $event)
    {
        if (module_is_active('Webhook')) {
            $selection = $event->selection;

            $web_array = [
                'Item Title' => $selection->name,
                'Item Type' => $selection->type,
                'Item Price' => $selection->price,
            ];

            $action = 'New Menu Items';
            $module = 'CateringManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
