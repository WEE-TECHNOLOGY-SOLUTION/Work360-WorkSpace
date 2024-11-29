<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Hrm\Events\CreateHolidays;

class CreateHolidayLis
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
    public function handle(CreateHolidays $event)
    {
        if(module_is_active('Webhook')){
            $holiday = $event->holiday;
            $action = 'New Holidays';
            $module = 'Hrm';
            SendWebhook::SendWebhookCall($module ,$holiday,$action);
        }
    }
}
