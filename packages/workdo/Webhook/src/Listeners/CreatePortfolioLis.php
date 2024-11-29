<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Portfolio\Entities\PortfolioCategory;
use Workdo\Portfolio\Events\CreatePortfolio;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePortfolioLis
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
    public function handle(CreatePortfolio $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $portfolio = $event->portfolio;

            $category = PortfolioCategory::find($portfolio->category);

            $web_array = [
                'Portfolio Title' => $portfolio->title,
                'Portfolio Category' => $category->title,
                'Portfolio Short Description' => $portfolio->short_description,
                'Portfolio Description' => strip_tags($portfolio->description),
            ];

            $action = 'New Portfolio';
            $module = 'Portfolio';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
