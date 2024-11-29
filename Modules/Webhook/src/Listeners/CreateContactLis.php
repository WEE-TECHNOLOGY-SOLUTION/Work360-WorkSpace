<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\VCard\Events\CreateContact;
class CreateContactLis
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
    public function handle(CreateContact $event)
    {
        if(module_is_active('Webhook')){
            $request=$event->request;
            $contact = $event->contact;
            $business_name   = \Workdo\VCard\Entities\Business::find($request->business_id);
            if(!empty($business_name))
            {
                // $contact->business_id = $business_name->title;
            }
            $action = 'New Contact';
            $module = 'VCard';
            SendWebhook::SendWebhookCall($module ,$contact,$action);
        }
    }
}