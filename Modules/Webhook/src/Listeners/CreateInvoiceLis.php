<?php

namespace Workdo\Webhook\Listeners;

use App\Events\CreateInvoice;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Taskly\Entities\Project;
use Workdo\Webhook\Entities\SendWebhook;

class CreateInvoiceLis
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
    public function handle(CreateInvoice $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $invoice = $event->invoice;

            if($invoice->invoice_module == "account" && $request->invoice_type == 'product'){
                if( array_column($request->items, 'item')){
                    $product =  array_column($request->items, 'item');
                    $product = \Workdo\ProductService\Entities\ProductService::whereIn('id',$product)->get()->pluck('name')->toArray();
                    if(count($product) > 0)
                    {
                        $product_name = implode(',',$product);
                    }
                    $invoice->product = $product_name;
                }
                if($invoice->user_id){
                    $customer = User::find($invoice->user_id);
                    $invoice->customer_name = $customer->name;
                }
                if($invoice->category_id){
                    $category = \Workdo\ProductService\Entities\Category::where('id',$request->category_id)->where('type', 1)->first();
                    $invoice->category_name = $category->name;
                }
                else{
                    $project = Project::find($request->project);
                    $invoice->category_name = $project->name;
                }
                unset($invoice->user_id);
            }
                $action = 'New Invoice';
                $module = 'general';
                SendWebhook::SendWebhookCall($module ,$invoice,$action);
        }
    }
}