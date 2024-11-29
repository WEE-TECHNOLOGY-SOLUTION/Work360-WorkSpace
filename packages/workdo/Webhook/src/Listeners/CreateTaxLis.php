<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Events\CreateTax;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTaxLis
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
    public function handle(CreateTax $event)
    {
        if (module_is_active('Webhook')) {
            $tax = $event->tax;

            $web_array = [
                'Tax Title' => $tax->name,
                'Tax Rate' => $tax->rate
            ];

            $action = 'New Tax';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
