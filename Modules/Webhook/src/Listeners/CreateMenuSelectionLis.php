<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CateringManagement\Entities\CateringSelection;
use Workdo\CateringManagement\Entities\MenuSelection;
use Workdo\CateringManagement\Events\CreateMenuSelection;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMenuSelectionLis
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
    public function handle(CreateMenuSelection $event)
    {
        if (module_is_active('Webhook')) {
            $menusection = $event->menusection;
            $request = $event->request;

            $selectionIds = explode(',', $menusection['selection_id']);

            $selections = CateringSelection::whereIn('id', $selectionIds)->get();

            $selectionData = collect($selections)->map(function ($selection) {
                return [
                    'name' => $selection->name,
                    'price' => $selection->price,
                    'type' => $selection->type,
                ];
            })->toArray();

            $web_array = [
                'Menu Title' => $menusection->name,
                'Menu Special Request' => $menusection->special_request,
                'Menu Items' => $selectionData,
                'Request Price' => $menusection->request_price,
                'Total Price' => $menusection->total,
            ];

            $action = 'New Menu Selection';
            $module = 'CateringManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
