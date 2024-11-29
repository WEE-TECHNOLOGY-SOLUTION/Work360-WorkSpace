<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Events\CreateMovieCrew;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMovieCrewLis
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
    public function handle(CreateMovieCrew $event)
    {
        if (module_is_active('Webhook')) {
            $moviecrew = $event->moviecrew;

            $web_array = [
                'Movie Crew' => $moviecrew->name
            ];

            $action = 'New Movie Crew';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
