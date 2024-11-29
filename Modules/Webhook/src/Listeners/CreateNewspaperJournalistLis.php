<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperJournalist;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperJournalistLis
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
    public function handle(CreateNewspaperJournalist $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $user = $event->user;
            $journalistDetails = $event->journalistDetails;

            $web_array = [
                'Journalist Name' => $user->name,
                'Journalist Email' => $user->email,
                'Journalist Phone Number' => $user->mobile_no,
                'Journalist Area' => $journalistDetails->area,
                'Journalist City' => $journalistDetails->city,
                'Journalist Address' => $journalistDetails->address
            ];

            $action = 'New Journalist';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
