<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\LMS\Events\CreateBlog;

class CreateBlogLis
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
    public function handle(CreateBlog $event)
    {
        if(module_is_active('Webhook'))
        {
            $blog = $event->blog;
            if(!empty($blog))
            {
                $action = 'New Blog';
                $module = 'LMS';
                SendWebhook::SendWebhookCall($module ,$blog,$action);
            }
        }
    }
}
