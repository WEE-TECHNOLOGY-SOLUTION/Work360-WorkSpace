<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Entities\NewspaperJournalistType;
use Workdo\Newspaper\Events\CreateNewspaperJournalistInfo;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperJournalistInfoLis
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
    public function handle(CreateNewspaperJournalistInfo $event)
    {
        if (module_is_active('Webhook')) {
            $information = $event->information;

            $type = NewspaperJournalistType::find($information->type);
            $user = User::find($information->user_id);

            $web_array = [
                'Information Title' => $information->name,
                'Jounalist Type' => $type->name,
                'User Name' => $user->name,
                'User Email' => $user->email,
                'Information Date' => $information->date,
                'Information' => $information->info,
            ];

            $action = 'New Journalist Information';
            $module = 'Newspaper';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
