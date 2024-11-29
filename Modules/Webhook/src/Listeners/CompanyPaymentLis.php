<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;

class CompanyPaymentLis
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
    public function handle($event)
    {
        $type = $event->type;
        $payment = $event->payment;
        $data = $event->data;
        if ($type == 'invoice') {
            if (!empty($payment) && !empty($data)) {
                $action = 'Invoice Status Updated';
                $module = 'general';
                SendWebhook::SendWebhookCall($module, $payment, $action, $data->workspace);
            }
        } elseif ($type == 'salesinvoice') {
            if (!empty($payment) && !empty($data)) {
                $action = 'New Sales Invoice Payment';
                $module = 'Sales';
                SendWebhook::SendWebhookCall($module, $payment, $action, $data->workspace);
            }
        } elseif ($type == 'retainer') {
            if (!empty($payment) && !empty($data)) {
                $action = 'New Retainer Payment';
                $module = 'Retainer';
                SendWebhook::SendWebhookCall($module, $payment, $action, $data->workspace);
            }
        }
    }
}
