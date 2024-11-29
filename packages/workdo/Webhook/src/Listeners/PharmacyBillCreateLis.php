<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PharmacyManagement\Entities\Medicine;
use Workdo\PharmacyManagement\Entities\MedicineCategory;
use Workdo\PharmacyManagement\Entities\MedicineType;
use Workdo\PharmacyManagement\Events\PharmacyBillCreate;
use Workdo\Webhook\Entities\SendWebhook;

class PharmacyBillCreateLis
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
    public function handle(PharmacyBillCreate $event)
    {
        if (module_is_active('Webhook')) {
            $PharmacyBill = $event->PharmacyBill;
            $request = $event->request;

            $vendor = User::find($PharmacyBill->user_id);

            $items = $request->items;

            $categoryIds = array_unique(array_column($items, 'category'));
            $typeIds = array_unique(array_column($items, 'type'));
            $medicineIds = array_unique(array_column($items, 'medicine'));

            $categoryNames = MedicineCategory::whereIn('id', $categoryIds)->pluck('name', 'id')->toArray();
            $typeNames = MedicineType::whereIn('id', $typeIds)->pluck('name', 'id')->toArray();
            $medicineNames = Medicine::whereIn('id', $medicineIds)->pluck('name', 'id')->toArray();

            $pharmacyBillItems = array_map(function ($item) use ($categoryNames, $typeNames, $medicineNames) {
                return [
                    'category' => $categoryNames[$item['category']] ?? null,
                    'type' => $typeNames[$item['type']] ?? null,
                    'medicine' => $medicineNames[$item['medicine']] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'discount' => $item['discount'],
                    'description' => $item['description'],
                ];
            }, $items);

            $web_array = [
                'Vendor Name' => $vendor->name,
                'Vendor Email' => $vendor->email,
                'Bill Issue Date' => $PharmacyBill->issue_date,
                'Bill Due Date' => $PharmacyBill->due_date,
                'Items' => $pharmacyBillItems,
            ];

            $action = 'New Pharmacy Bill';
            $module = 'PharmacyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
