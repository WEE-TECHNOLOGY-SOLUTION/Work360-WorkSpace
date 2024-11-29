<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Events\CreateFeeReciept;
use Workdo\ProductService\Entities\Tax;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFeeRecieptLis
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
    public function handle(CreateFeeReciept $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $feeReciept = $event->feeReciept;

            $advocate = User::find($feeReciept->advocate);
            $bill_to = User::find($feeReciept->bill_to);

            $itemsArray = $feeReciept["items"];

            $taxIds = collect($itemsArray)->pluck('tax')->unique()->toArray();
            $taxTitles = Tax::whereIn('id', $taxIds)->pluck('name', 'id')->all();

            foreach ($itemsArray as &$item) {
                $item['tax'] = $taxTitles[$item['tax']] ?? $item['tax'];
            }

            array_walk($itemsArray, function (&$item) {
                unset ($item['id']);
            });

            $feeReciept["items"] = $itemsArray;

            $web_array = [
                'Bill From' => $feeReciept->bill_from,
                'Advocate' => $advocate->name,
                'Bill To' => $bill_to->name,
                'Reciept Title' => $feeReciept->title,
                'Bill Number' => $feeReciept->bill_number,
                'Reciept Date' => $feeReciept->reciept_date,
                'Items' => $feeReciept["items"],
                'Due Date' => $feeReciept->due_date,
                'Sub Total' => $feeReciept->subtotal,
                'Total Tax' => $feeReciept->total_tax,
                'Total Amount' => $feeReciept->total_amount,
                'Due Amount' => $feeReciept->due_amount,
                'Description' => $feeReciept->description,
                'Total Discount' => $feeReciept->total_disc,
            ];
            $action = 'New Fee Reciept';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
