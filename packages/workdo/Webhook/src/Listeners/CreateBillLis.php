<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\CreateBill;
use Workdo\Taskly\Entities\Project;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBillLis
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
    public function handle(CreateBill $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $bill = $event->bill;
            $category_data = null;

            if (array_column($request->items, 'item') && $request->bill_type == "product") {
                $product = array_column($request->items, 'item');
                $product = \Workdo\ProductService\Entities\ProductService::whereIn('id', $product)->get()->pluck('name')->toArray();
                if (count($product) > 0) {
                    $product_name = implode(',', $product);
                }
                if ($bill->category_id) {
                    $category = \Workdo\ProductService\Entities\Category::where('id', $bill->category_id)->where('type', 2)->first();
                    $category_data = $category->name;
                }
                $bill->product = $product_name;
            } else {
                $project = Project::find($request->project);
                $category_data = $project->name;
            }
            if ($bill->user_id) {
                $vendor = User::find($bill->user_id);
            }

            unset($bill->user_id);
            $action = 'New Bill';
            $module = 'Account';
            SendWebhook::SendWebhookCall($module, $bill, $action);
        }
    }
}