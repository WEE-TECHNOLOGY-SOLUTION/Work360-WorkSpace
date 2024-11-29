<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperTax;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperTaxLis
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
    public function handle(CreateNewspaperTax $event)
    {
        if (module_is_active('Webhook')) {
            $newspapertax = $event->newspapertax;

            $web_array = [
                'Tax Title' => $newspapertax->name,
                'Tax Percentage' => $newspapertax->percentage,
            ];

            $action = 'New Newspaper Tax';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
