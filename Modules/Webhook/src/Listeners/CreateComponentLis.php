<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CMMS\Entities\Location;
use Workdo\CMMS\Events\CreateComponent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateComponentLis
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
    public function handle(CreateComponent $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $components = $event->components;

            $location = Location::find($components->location_id);

            $web_array = [
                'Component Title' => $components->name,
                'Component SKU' => $components->sku,
                'Location' => $location->name,
                'Component Tag' => $request['Component_Tag']['Component_Tag'],
                'Component Category' => $request['Category']['Category'],
                'Assigned Date' => $request['Assigned_Date']['Assigned Date'],
                'Component Description' => $request['Description']['Description'],
                'Link' => $request['Link']['Link'],
                'Model' => $request['Model']['Model'],
                'Brand' => $request['Brand']['Brand'],
                'Operating Hours' => $request['Operating_Hours']['Operating Hours'],
                'Original Cost' => $request['Original_Cost']['Original Cost'],
                'Purchase Cost' => $request['Purchase_Cost']['Purchase Cost'],
                'Serial Number' => $request['Serial_Number']['Serial Number'],
                'Service Contact' => $request['Service_Contact']['Service Contact'],
                'Warranty Expired Date' => $request['Warranty_Exp_Date']['Warranty Exp Date'],
            ];

            $action = 'New Component';
            $module = 'CMMS';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
