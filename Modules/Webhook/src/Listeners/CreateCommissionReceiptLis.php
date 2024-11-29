<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Commission\Entities\CommissionModule;
use Workdo\Commission\Entities\CommissionPlan;
use Workdo\Commission\Events\CreateCommissionReceipt;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCommissionReceiptLis
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
    public function handle(CreateCommissionReceipt $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $commissionReceipt = $event->commissionReceipt;

            $modules = CommissionModule::find($commissionReceipt->commission_str);
            $commission_plan = CommissionPlan::find($commissionReceipt->commissionplan_id);
            $agent = User::find($commissionReceipt->agent);

            $commissionReceipt->structure_title = $modules->module;
            $commissionReceipt->sub_structure = $modules->submodule;
            $commissionReceipt->commission_pan_title = $commission_plan->name;
            $commissionReceipt->agent_name = $agent->name;

            $action = 'New Commission Receipt';
            $module = 'Commission';
            SendWebhook::SendWebhookCall($module, $commissionReceipt, $action);
        }
    }
}
