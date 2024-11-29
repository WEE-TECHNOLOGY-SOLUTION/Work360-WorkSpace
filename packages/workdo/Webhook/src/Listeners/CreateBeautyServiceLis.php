<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeautySpaManagement\Events\CreateBeautyService;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBeautyServiceLis
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
    public function handle(CreateBeautyService $event)
    {
        if (module_is_active('Webhook')) {
            $beautyservice = $event->beautyservice;

            $web_array = [
                'Service Title' => $beautyservice->name,
                'Service Price' => $beautyservice->price,
                'Service Time' => $beautyservice->time
            ];

            $action = 'New Beauty Service';
            $module = 'BeautySpaManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
