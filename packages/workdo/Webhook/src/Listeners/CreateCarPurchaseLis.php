<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Entities\DealershipProduct;
use Workdo\CarDealership\Entities\Tax;
use Workdo\CarDealership\Events\CreateCarPurchase;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCarPurchaseLis
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
    public function handle(CreateCarPurchase $event)
    {
        if (module_is_active('Webhook')) {
            $car_purchase = $event->car_purchase;
            $request = $event->request;

            $vendor = User::find($car_purchase->vendor_id);

            $items = $request->items;

            $productIds = array_unique(array_column($items, 'item'));
            $typeIds = array_unique(array_column($items, 'type'));

            $productNames = DealershipProduct::whereIn('id', $productIds)->pluck('name', 'id')->toArray();
            $taxNames = Tax::whereIn('id', $typeIds)->pluck('name', 'id')->toArray();

            $dealershipProductItems = array_map(function ($item) use ($productNames, $taxNames) {
                return [
                    'Product Type' => $item['product_type'],
                    'Product Name' => $productNames[$item['item']] ?? null,
                    'Quantity' => $item['quantity'],
                    'Price' => $item['price'],
                    'Discount' => $item['discount'],
                    'Item Tax Price' => $item['itemTaxPrice'],
                    'Item Tax Rate' => $item['itemTaxRate'],
                    'Description' => $item['description'],
                ];
            }, $items);

            $web_array = [
                'Purchase Date' => $car_purchase->purchase_date,
                'Due Date' => $car_purchase->due_date,
                'Vendor Name' => $vendor->name,
                'Vendor Email' => $vendor->email,
                'Items' => $dealershipProductItems
            ];

            $action = 'New Car Purchase';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
