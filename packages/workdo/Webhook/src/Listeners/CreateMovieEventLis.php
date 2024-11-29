<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Entities\MovieShow;
use Workdo\MovieShowBookingSystem\Events\CreateMovieEvent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMovieEventLis
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
    public function handle(CreateMovieEvent $event)
    {
        if (module_is_active('Webhook')) {
            $movieevent = $event->movieevent;

            $movieShow = MovieShow::find($movieevent->movie_show_id);

            $web_array = [
                'Movie Title' => $movieShow->movie_name,
                'Movie Show Start Time' => $movieevent->show_start_time,
                'Movie Show End Time' => $movieevent->show_start_time,
                'Movie Show Start Date' => $movieevent->start_date,
                'Movie Show End Date' => $movieevent->end_date,
                'Movie Vanue' => $movieevent->venue
            ];

            $action = 'New Movie Event';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
