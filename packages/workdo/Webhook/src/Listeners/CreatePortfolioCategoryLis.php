<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Portfolio\Events\PortfolioCategoryCreate;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePortfolioCategoryLis
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
    public function handle(PortfolioCategoryCreate $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $portfolioCategory = $event->portfolioCategory;

            $web_array = [
                'Portfolio Category Title' => $portfolioCategory->title,
                'Portfolio Categoey Description' => $portfolioCategory->description
            ];

            $action = 'New Portfolio Category';
            $module = 'Portfolio';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
