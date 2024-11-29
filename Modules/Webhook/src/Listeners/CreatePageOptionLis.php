<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreatePageOption;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePageOptionLis
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
    public function handle(CreatePageOption $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $custom_page = $event->custom_page;

            $web_array = [
                "Custom Page Title" => $custom_page->name,
                "Custom Page Content" => strip_tags($custom_page->contents),
            ];

            $action = 'New Page Option';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
