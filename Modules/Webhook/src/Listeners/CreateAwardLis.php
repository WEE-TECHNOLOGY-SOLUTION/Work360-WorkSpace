<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Hrm\Entities\AwardType;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Hrm\Events\CreateAward;

class CreateAwardLis
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
    public function handle(CreateAward $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $award = $event->award;
            $employee = User::where('id', $request->employee_id)->first();
            $awardtype = AwardType::find($request->award_type);
            $award->employee_name = $employee->name;
            if(!empty($awardtype))
            {
                $award->award_type = $awardtype->name;
            }
            unset($award->user_id);
            $action = 'New Award';
            $module = 'Hrm';
            SendWebhook::SendWebhookCall($module ,$award,$action);
        }
    }
}
