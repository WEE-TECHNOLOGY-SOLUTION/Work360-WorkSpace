<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Entities\NewspaperTax;
use Workdo\Newspaper\Entities\NewspaperVarient;
use Workdo\Newspaper\Events\CreateNewspaper;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperLis
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
    public function handle(CreateNewspaper $event)
    {
        if (module_is_active('Webhook')) {
            $newspaper = $event->newspaper;

            $varient = NewspaperVarient::find($newspaper->varient);
            $tax = NewspaperTax::find($newspaper->taxes);

            $web_array = [
                'News Paper Title' => $newspaper->name,
                'News Paper Date' => $newspaper->date,
                'News Paper Varient' => $varient->name,
                'News Papaer Tax' => $tax->name,
                'News Paper Quantity' => $newspaper->quantity,
                'News Paper Price' => $newspaper->price,
                'News Paper Sale Price' => $newspaper->seles_price
            ];

            $action = 'New Newspaper';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
