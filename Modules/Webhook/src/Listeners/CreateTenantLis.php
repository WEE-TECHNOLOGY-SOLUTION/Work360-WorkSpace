<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PropertyManagement\Entities\Property;
use Workdo\PropertyManagement\Entities\PropertyUnit;
use Workdo\PropertyManagement\Events\CreateTenant;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTenantLis
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
    public function handle(CreateTenant $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $tenant = $event->tenant;

            $property = Property::find($tenant->property_id);
            $propertyUnit = PropertyUnit::find($tenant->unit_id);
            $user = User::find($tenant->unit_id);

            $web_array = [
                'Customer Name' => $user->name,
                'Customer Email' => $user->email,
                'Customer Mobile Number' => $user->mobile_no,
                'Property Title' => $property->name,
                'Property Unit Title' => $propertyUnit->name,
                'Total Family Members' => $tenant->total_family_member,
                'Country' => $tenant->country,
                'State' => $tenant->state,
                'City' => $tenant->city,
                'Pincode' => $tenant->pincode,
                'Address' => $tenant->address,
                'Lease Start Date' => $tenant->lease_start_date,
                'Lease End Date' => $tenant->lease_end_date,
            ];

            $action = 'New Property Units';
            $module = 'PropertyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
