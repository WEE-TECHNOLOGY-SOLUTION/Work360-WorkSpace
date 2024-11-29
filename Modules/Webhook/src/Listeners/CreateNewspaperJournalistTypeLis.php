<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperJournalistType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperJournalistTypeLis
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
    public function handle(CreateNewspaperJournalistType $event)
    {
        if (module_is_active('Webhook')) {
            $journalisttype = $event->journalisttype;

            $web_array = [
                'Journalist Type Title' => $journalisttype->name,
                'Journalist Price' => $journalisttype->price,
            ];

            $action = 'New Journalist Type';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
