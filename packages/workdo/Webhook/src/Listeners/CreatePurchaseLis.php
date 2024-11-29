<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use App\Models\Warehouse;
use Workdo\Pos\Events\CreatePurchase;

class CreatePurchaseLis
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
    public function handle(CreatePurchase $event)
    {
        //webhook
        if(module_is_active('Webhook'))
        {
            $request = $event->request;
            $purchase = $event->purchase;

            if( array_column($request->items, 'item')){
                $product=  array_column($request->items, 'item');
                $product = \Workdo\ProductService\Entities\ProductService::whereIn('id',$product)->get()->pluck('name')->toArray();
                if(count($product) > 0)
                {
                    $product_name = implode(',',$product);
                }
                $purchase->product = $product_name;
            }
            if($purchase->user_id){
                $vendor = User::find($purchase->vender_id);
                // $purchase->vender_id = $vendor->name;
                // unset($purchase->vender_name);
            }else
            {
                unset($purchase->vender_id);
            }
            if($request->warehouse_id)
            {
                $warehouse = Warehouse::where('id',$request->warehouse_id)->first();
                // $purchase->warehouse_id = $warehouse->name;
            }
            if($purchase->category_id){
                $category = \Workdo\ProductService\Entities\Category::where('id',$request->category_id)->where('type', 2)->first();
                // $purchase->category_id = $category->name;
            }
            unset($purchase->user_id);

            $action = 'New Purchase';
            $module = 'Pos';
            SendWebhook::SendWebhookCall($module ,$purchase,$action);
        }
    }
}
