<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperCategory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperCategoryLis
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
    public function handle(CreateNewspaperCategory $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $newspapercategory = $event->newspapercategory;

            $web_array = [
                'News Paper Category Title' => $newspapercategory->name,
            ];

            $action = 'New Newspaper Varient';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
