<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeautySpaManagement\Events\UpdateWorkingHours;
use Workdo\Webhook\Entities\SendWebhook;

class UpdateWorkingHoursLis
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
    public function handle(UpdateWorkingHours $event)
    {
        if (module_is_active('Webhook')) {
            $beautyworking = $event->beautyworking;

            $web_array = [
                'Opening Time' => $beautyworking['opening_time'],
                'Closing Time' => $beautyworking['closing_time'],
                'Day of Weeks' => explode(',', $beautyworking['day_of_week']),
                'Holiday Settings' => $beautyworking['holiday_setting'],
            ];

            $action = 'Update Working Hours';
            $module = 'BeautySpaManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
