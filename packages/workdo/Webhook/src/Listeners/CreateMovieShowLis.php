<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Entities\Certificate;
use Workdo\MovieShowBookingSystem\Entities\MovieCast;
use Workdo\MovieShowBookingSystem\Entities\MovieCrew;
use Workdo\MovieShowBookingSystem\Entities\SeatingTemplate;
use Workdo\MovieShowBookingSystem\Entities\ShowType;
use Workdo\MovieShowBookingSystem\Events\CreateMovieShow;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMovieShowLis
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
    public function handle(CreateMovieShow $event)
    {
        if (module_is_active('Webhook')) {
            $movieshow = $event->movieshow;

            $movie_type_id = explode(',', $movieshow->movie_type);

            $movieTypeDataArray = [];

            foreach ($movie_type_id as $movieTypeId) {
                $movieType = ShowType::find($movieTypeId);

                if ($movieType) {
                    $movieTypeDataArray[] = [
                        'Movie Type Name' => $movieType->name,
                    ];
                }
            }

            $movie_cast_id = explode(',', $movieshow->movie_cast);

            $movieCastArray = [];

            foreach ($movie_cast_id as $movieCastId) {
                $movieCast = MovieCast::find($movieCastId);

                if ($movieCast) {
                    $movieCastArray[] = [
                        'Movie Cast' => $movieCast->cast_name,
                    ];
                }
            }

            $movie_crew = MovieCrew::find($movieshow->movie_crew);
            $movie_certificate = Certificate::find($movieshow->certificate);
            $seating_template = SeatingTemplate::find($movieshow->seating_template);

            $web_array = [
                'Movie Name' => $movieshow->movie_name,
                'Movie Type' => $movieTypeDataArray,
                'Movie Show Date' => $movieshow->show_date,
                'Movie Release Date' => $movieshow->release_date,
                'Movie Cast' => $movieCastArray,
                'Movie Crew' => $movie_crew->name,
                'Movie Certificate' => $movie_certificate->name,
                'Movie Language' => $movieshow->language,
                'Movie Venue' => $movieshow->show_time,
                'Seating Layout Title' => $seating_template->name,
            ];

            $action = 'New Movie Show';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
