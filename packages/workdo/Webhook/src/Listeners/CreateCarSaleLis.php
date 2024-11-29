<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Entities\DealershipProduct;
use Workdo\CarDealership\Entities\Tax;
use Workdo\CarDealership\Events\CreateCarSale;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCarSaleLis
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
    public function handle(CreateCarSale $event)
    {
        if (module_is_active('Webhook')) {
            $car_sale = $event->car_sale;
            $request = $event->request;

            $customer = User::find($car_sale->customer_id);

            $items = $request->items;

            $productIds = array_unique(array_column($items, 'item'));
            $typeIds = array_unique(array_column($items, 'type'));

            $productNames = DealershipProduct::whereIn('id', $productIds)->pluck('name', 'id')->toArray();
            $taxNames = Tax::whereIn('id', $typeIds)->pluck('name', 'id')->toArray();

            $dealershipProductItems = array_map(function ($item) use ($productNames, $taxNames) {
                return [
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
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Issue Date' => $car_sale->issue_date,
                'Due Date' => $car_sale->due_date,
                'Sale Type' => $request->sale_type,
                'Dealership Product Item' => $dealershipProductItems,
            ];

            $action = 'New Car Sale';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
