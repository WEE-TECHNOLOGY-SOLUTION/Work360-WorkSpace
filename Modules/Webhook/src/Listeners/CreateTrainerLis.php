<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Training\Events\CreateTrainer;

class CreateTrainerLis
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
    public function handle(CreateTrainer $event)
    {
        if (module_is_active('Webhook')) {
            $trainer = $event->trainer;
            $request = $event->request;
            $branch = \Workdo\Hrm\Entities\Branch::where('id', $request->branch)->first();
            if (!empty($branch)) {

                // $trainer->branch     = $branch->name;
            }
            $action = 'New Trainer';
            $module = 'Training';
            SendWebhook::SendWebhookCall($module, $trainer, $action);
        }
    }
}
