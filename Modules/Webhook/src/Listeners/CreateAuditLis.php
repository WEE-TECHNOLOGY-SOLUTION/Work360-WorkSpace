<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreateAudit;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAuditLis
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
    public function handle(CreateAudit $event)
    {
        if (module_is_active('Webhook')) {

            $request = $event->request;
            $audit = $event->audit;

            $assetIds = explode(',', $audit['asset']);
            $assets = FixAsset::WhereIn('id', $assetIds)->get()->pluck('title');
            $assetArray = $assets->toArray();

            $titlesAndQuantitiesArray = [];

            foreach ($audit['audit_data'] as $item) {
                $title = $item->title;
                $quantity = $item->quantity;

                $titlesAndQuantitiesArray[] = [
                    'title' => $title,
                    'quantity' => $quantity,
                ];
            }

            $web_array = [
                'Audit Title' => $audit->audit_title,
                'Audit Date' => $audit->audit_date,
                'Audit Status' => $audit->audit_status,
                'Assets' => $assetArray,
                'Audit Data' => $titlesAndQuantitiesArray,
            ];

            $action = 'New Audit';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
