<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Portfolio\Events\UpdatePortfolioStatus;
use Workdo\Webhook\Entities\SendWebhook;

class UpdatePortfolioStatusLis
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
    public function handle(UpdatePortfolioStatus $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $item = $event->item;

            $web_array = [
                'Title' => $item->title,
                'Portfolio Short Description' => $item->short_description,
                'Portfolio Description' => strip_tags($item->description),
                'Portfolio Status' => $item->enabled == 1 ? 'Enable' : 'Diabled',
            ];

            $action = 'Update Portfolio Status';
            $module = 'Portfolio';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
