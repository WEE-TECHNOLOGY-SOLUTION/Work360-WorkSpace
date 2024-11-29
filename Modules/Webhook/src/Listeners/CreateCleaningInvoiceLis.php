<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CleaningManagement\Entities\CleaningBooking;
use Workdo\CleaningManagement\Entities\CleaningInspection;
use Workdo\CleaningManagement\Events\CreateCleaningInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCleaningInvoiceLis
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
    public function handle(CreateCleaningInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $invoice_id = $event->id;
            $invoice = $event->invoice;

            $inspection = CleaningInspection::find($invoice->inspection_id);
            $booking = CleaningBooking::find($inspection->booking_id);

            $web_array = [
                'Customer Name' => $booking->customer_name,
                'Building Type' => $booking->building_type,
                'Customer Address' => $booking->address,
                'Description' => $booking->description,
                'Booking Date' => $booking->booking_date,
                'Cleaning Date' => $booking->cleaning_date,
                'Inspection Status' => $inspection->status == 1 ? 'Cleaned' : 'Dirt',
            ];

            $action = 'New Cleaning Invoice';
            $module = 'CleaningManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
