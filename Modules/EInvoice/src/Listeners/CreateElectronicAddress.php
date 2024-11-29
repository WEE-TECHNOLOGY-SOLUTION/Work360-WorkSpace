<?php

namespace Workdo\EInvoice\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Account\Entities\Customer;

class CreateElectronicAddress
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

    public function handle(CreateCustomer $event)
    {
        $request = $event->request;
        $customer = $event->customer;
        $newCustomer = Customer::find($customer->id);
        $newCustomer->electronic_address = $request->electronic_address;
        $newCustomer->electronic_address_scheme = $request->electronic_address_scheme;
        $newCustomer->save();
    }
}
