<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeautySpaManagement\Entities\BeautyService;
use Workdo\BeautySpaManagement\Events\CreateBeautyBooking;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBeautyBookingLis
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
    public function handle(CreateBeautyBooking $event)
    {
        if (module_is_active('Webhook')) {
            $beautybooking = $event->beautybooking;

            $service = BeautyService::find($beautybooking->service);

            $web_array = [
                'Name' => $beautybooking->name,
                'Service Title' => $service->name,
                'Service Price' => $service->price,
                'Service Time' => $service->time,
                'Booking Date' => $beautybooking->date,
                'Customer Mobile Number' => $beautybooking->number,
                'Customer Email' => $beautybooking->email
            ];

            $action = 'New Beauty Booking';
            $module = 'BeautySpaManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
