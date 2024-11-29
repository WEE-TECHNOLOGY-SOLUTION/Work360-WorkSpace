<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceTax;

class CreateWoocommerceTaxLis
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
    public function handle(CreateWoocommerceTax $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $Tax = $event->Tax;

            $webhook = [
                "Name" => $request['name'],
                "Type" => $Tax->type,
                "Country" => $request['country'],
                "State" => $request['state'],
                "City" => $request['city'],
                "Rate" => $request['rate']
            ];

            $action = 'New Tax';
            $module = 'WordpressWoocommerce';
            SendWebhook::SendWebhookCall($module, $webhook, $action);
        }
    }
}
