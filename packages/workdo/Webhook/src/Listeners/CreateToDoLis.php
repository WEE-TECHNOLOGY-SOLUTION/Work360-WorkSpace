<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ToDo\Events\CreateToDo;
use Workdo\Webhook\Entities\SendWebhook;

class CreateToDoLis
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
    public function handle(CreateToDo $event)
    {
        if (module_is_active('Webhook')) {
            $toDo = $event->toDo;
            $request = $event->request;

            $usersData = User::whereIn('id', $request->assigned_to)->get();

            $usersInfo = [];
            foreach ($usersData as $user) {
                $usersInfo[] = [
                    'Name' => $user->name,
                    'Email' => $user->email,
                ];
            }

            $web_array = [
                'Title' => $toDo->title,
                'Assign To' => $usersInfo,
                'Priority' => $toDo->priority,
                'Start Date' => $toDo->start_date,
                'Due Date' => $toDo->due_date,
                'Description' => $toDo->description,
            ];

            $action = 'New To Do';
            $module = 'ToDo';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
