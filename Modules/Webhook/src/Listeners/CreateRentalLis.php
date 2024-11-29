<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ProductService\Entities\ProductService;
use Workdo\RentalManagement\Entities\Rental;
use Workdo\RentalManagement\Events\CreateRental;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRentalLis
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
    public function handle(CreateRental $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $rental = $event->rental;

            $ProgramItems = $request->items;
            foreach ($ProgramItems as $programItem) {
                $data = [
                    'Product Type' => $programItem['product_type'],
                    'Quantity' => $programItem['quantity'],
                    'Price' => $programItem['price'],
                    'discount' => $programItem['discount'],
                    'itemTaxPrice' => $programItem['itemTaxPrice'],
                ];

                $allData[] = $data;
            }

            $customer = User::find($rental->customer_id);

            $itemArray = [];
            foreach ($request['items'] as $item) {
                $itemArray[] = $item['item'];
            }

            $items = ProductService::whereIn('id', $itemArray)->get();

            $attributesArray = [];

            foreach ($items as $item) {
                $attributes = [
                    'Name' => $item->name,
                    'SKU' => $item->sku,
                    'Sale Price' => $item->sale_price,
                    'Purchase Price' => $item->purchase_price,
                    'Quantity' => $item->quantity,
                ];
                $attributesArray[] = $attributes;
            }

            $web_array = [
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Rental Type' => Rental::$types[$rental->rent_type],
                'Start Date' => $rental->start_date,
                'End Date' => $rental->end_date,
                'Items' => $allData,
                "Item Details" => $attributesArray
            ];

            $action = 'New Rental';
            $module = 'RentalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
