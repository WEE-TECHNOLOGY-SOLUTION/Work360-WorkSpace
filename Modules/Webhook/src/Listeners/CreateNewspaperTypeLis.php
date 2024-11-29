<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperTypeLis
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
    public function handle(CreateNewspaperType $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $newspapertype = $event->newspapertype;

            $web_array = [
                'News Paper Type' => $newspapertype->name
            ];

            $action = 'New Newspaper Type';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
