<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Rotas\Entities\Designation;
use Workdo\Rotas\Entities\Employee;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Rotas\Events\CreateRota;

class CreateRotaLis
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
    public function handle(CreateRota $event)
    {
        $rotas = $event->rotas;

        if (module_is_active('Webhook')) {
            $employee = Employee::where('id', $rotas->user_id)->first();
            $designation = Designation::where('id', $employee->designation_id)->first();

            $action = 'New Rota';
            $module = 'Rotas';

            SendWebhook::SendWebhookCall($module, $rotas, $action);
        }
    }
}
