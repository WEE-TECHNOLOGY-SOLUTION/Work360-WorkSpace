<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Entities\Distribution;
use Workdo\Newspaper\Entities\Newspaper;
use Workdo\Newspaper\Entities\NewspaperCategory;
use Workdo\Newspaper\Events\CreateNewspaperAds;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperAdsLis
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
    public function handle(CreateNewspaperAds $event)
    {
        if (module_is_active('Webhook')) {
            $ad = $event->ad;

            $newspaper = Newspaper::find($ad->newspaper);
            $category = NewspaperCategory::find($ad->category);
            $distributions = Distribution::find($ad->distributions);

            $web_array = [
                'Advertisement Title' => $ad->name,
                'Advertisement Date' => $ad->date,
                'News Paper Title' => $newspaper->name,
                'News Paper Date' => $newspaper->date,
                'News Paper Category' => $category->name,
                'News Paper Distribution Title' => $distributions->name,
                'News Paper Distribution Address' => $distributions->address,
            ];

            $action = 'New Advertisement';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
