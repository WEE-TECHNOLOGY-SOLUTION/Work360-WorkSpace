<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Internalknowledge\Events\CreateBook;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBookLis
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
    public function handle(CreateBook $event)
    {
        if (module_is_active('Webhook')) {
            $book = $event->book;

            $users = explode(',', $book->user_id);

            $usersData = User::whereIn('id', $users)->get();

            $usersInfo = [];
            foreach ($usersData as $user) {
                $usersInfo[] = [
                    'Name' => $user->name,
                    'Email' => $user->email,
                ];
            }

            $web_array = [
                'Book title' => $book->title,
                'Book Description' => $book->description,
                'Users' => $usersInfo
            ];

            $action = 'New Book';
            $module = 'Internalknowledge';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
