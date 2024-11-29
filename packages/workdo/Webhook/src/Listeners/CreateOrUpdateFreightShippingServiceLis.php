<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Entities\FreightService;
use Workdo\FreightManagementSystem\Events\CreateOrUpdateFreightShippingService;
use Workdo\Webhook\Entities\SendWebhook;

class CreateOrUpdateFreightShippingServiceLis
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
    public function handle(CreateOrUpdateFreightShippingService $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $shipping = $event->shipping;
            $service = $event->service;

            $items = $request->items;

            $vendorIds = array_unique(array_column($items, 'vendor'));
            $serviceIds = array_unique(array_column($items, 'service'));

            $vendorNames = User::whereIn('id', $vendorIds)->pluck('name', 'id')->toArray();
            $serviceNames = FreightService::whereIn('id', $serviceIds)->pluck('name', 'id')->toArray();

            $processedItems = array_map(function ($item) use ($vendorNames, $serviceNames) {
                return [
                    'vendor' => $vendorNames[$item['vendor']] ?? null,
                    'service' => $serviceNames[$item['service']] ?? null,
                    'qty' => $item['qty'],
                    'sale_price' => $item['sale_price'],
                    'total_sale_price' => $item['total_sale_price'],
                ];
            }, $items);

            $web_array = [
                'Customer Name' => $shipping->customer_name,
                'Customer Email' => $shipping->customer_email,
                'Direction' => $shipping->direction,
                'Transport' => $shipping->transport,
                'Loading Port' => $shipping->loading_port,
                'Discharge Port' => $shipping->discharge_port,
                'Vessel' => $shipping->vessel,
                'Date' => $shipping->date,
                'Barcode' => $shipping->barcode,
                'Tracking Number' => $shipping->tracking_no,
                'Items' => $processedItems
            ];

            $action = 'Update Freight Shipping Service';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
