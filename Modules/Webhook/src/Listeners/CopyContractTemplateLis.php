<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ContractTemplate\Events\CopyContractTemplate;
use Workdo\Webhook\Entities\SendWebhook;

class CopyContractTemplateLis
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
    public function handle(CopyContractTemplate $event)
    {
        if (module_is_active('Webhook')) {
            $contract_template = $event->contract_template;

            $web_array = [
                'Contract Template Subject' => $contract_template->subject,
                'Contract Template Type' => $contract_template->contract_type,
                'Contract Template Description' => strip_tags($contract_template->description),
            ];

            $action = 'Duplicate Contract Template';
            $module = 'ContractTemplate';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
