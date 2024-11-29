<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreateComponent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixEquipmentComponentsLis
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
    public function handle(CreateComponent $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $component = $event->component;

            $asset = FixAsset::find($component->asset);
            $category = EquipmentCategory::find($component->category);

            $web_array = [
                'Component Title' => $component->title,
                'Component Quantity' => $component->quantity,
                'Component Price' => $component->price,
                'Component Category' => $category->title,
                'Asset Title' => $asset->title,
                'Asset Purchase Date' => $asset->purchase_date,
            ];

            $action = 'New Component';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
