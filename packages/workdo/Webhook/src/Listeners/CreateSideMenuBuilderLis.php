<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SideMenuBuilder\Entities\SideMenuBuilder;
use Workdo\SideMenuBuilder\Events\CreateSideMenuBuilder;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSideMenuBuilderLis
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
    public function handle(CreateSideMenuBuilder $event)
    {
        if (module_is_active('Webhook')) {
            $menu_builder = $event->menu_builder;

            $parent = SideMenuBuilder::find($menu_builder->parent_id);

            $web_array = [
                'Manu Type' => $menu_builder->menu_type,
                'Title' => $menu_builder->name,
                'Link Type' => $menu_builder->link_type,
                'Parent' => isset($parent->name) ? $parent->name : '',
                'Link' => $menu_builder->link,
                'Window Type' => $menu_builder->window_type,
            ];

            $action = 'New Side Menu';
            $module = 'SideMenuBuilder';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
