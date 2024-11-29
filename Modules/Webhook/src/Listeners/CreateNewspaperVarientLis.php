<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperVarient;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperVarientLis
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
    public function handle(CreateNewspaperVarient $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $newspapervariant = $event->newspapervariant;

            $web_array = [
                'New Paper Varient' => $newspapervariant->name,
                'Varient Price' => $newspapervariant->price,
            ];

            $action = 'New Newspaper Category';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
