<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\LMS\Events\CreateCustomPage;

class CreateCustomPageLis
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
    public function handle(CreateCustomPage $event)
    {
        if(module_is_active('Webhook'))
        {
            $pageOption = $event->pageOption;
            if(!empty($pageOption))
            {
                $action = 'New Custom Page';
                $module = 'LMS';
                SendWebhook::SendWebhookCall($module ,$pageOption,$action);
            }
        }
    }
}
