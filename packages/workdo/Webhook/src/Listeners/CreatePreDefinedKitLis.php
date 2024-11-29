<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\AssetComponents;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreatePreDefinedKit;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePreDefinedKitLis
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
    public function handle(CreatePreDefinedKit $event)
    {
        if (module_is_active('Webhook')) {

            $kit = $event->kit;
            $request = $event->request;

            $asset = FixAsset::find($kit->asset);
            $component = AssetComponents::find($kit->component);

            $web_array = [
                'Pre Defined Kit Title' => $kit->title,
                'Asset Title' => $asset->title,
                'Asset Purchase Date' => $asset->purchase_date,
                'Component Title' => $component->title,
            ];

            $action = 'New Pre Defined Kit';
            $module = 'FixEquipment';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
