<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Entities\CastType;
use Workdo\MovieShowBookingSystem\Events\CreateMovieCast;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMovieCastLis
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
    public function handle(CreateMovieCast $event)
    {
        if (module_is_active('Webhook')) {
            $moviecast = $event->moviecast;

            $movie_cast = CastType::find($moviecast->cast_type);

            $web_array = [
                'Movie Title' => $moviecast->movie_title,
                'Cast Name' => $moviecast->cast_name,
                'Movie Cast Type' => $movie_cast->name
            ];

            $action = 'New Movie Cast';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
