<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Exam\Events\CreateExamHall;
use Workdo\Webhook\Entities\SendWebhook;

class CreateExamHallLis
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
    public function handle(CreateExamHall $event)
    {
        if (module_is_active('Webhook')) {
            $examhall = $event->examhall;

            $web_array = [
                'Exam Hall Title' => $examhall->hall_name,
                'Exam Hall Capacity' => $examhall->hall_capacity,
                'Description' => $examhall->description
            ];

            $action = 'New Exam Hall';
            $module = 'Exam';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
