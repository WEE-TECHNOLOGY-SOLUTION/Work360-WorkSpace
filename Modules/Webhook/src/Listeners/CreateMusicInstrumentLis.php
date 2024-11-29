<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MusicInstitute\Events\CreateMusicInstrument;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMusicInstrumentLis
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
    public function handle(CreateMusicInstrument $event)
    {
        if (module_is_active('Webhook')) {
            $instrument = $event->instrument;

            $web_array = [
                'Instrument Name' => $instrument->name,
                'Instrument Brand' => $instrument->brand,
                'Instrument Model' => $instrument->model,
                'Instrument Purchase Date' => $instrument->purchase_date,
                'Instrument Price' => $instrument->price,
                'Instrument Quantity' => $instrument->quantity,
                'Instrument Warranty' => $instrument->warranty,
                'Notes' => $instrument->notes,
                'Technician' => $instrument->technician,
            ];

            $action = 'New Music Instrument';
            $module = 'MusicInstitute';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
