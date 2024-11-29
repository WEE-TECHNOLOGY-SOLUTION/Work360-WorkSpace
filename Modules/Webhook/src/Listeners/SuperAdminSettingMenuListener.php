<?php

namespace Workdo\Webhook\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'Webhook';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Webhook',
            'name' => 'webhook',
            'order' => 700,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'webhook-sidenav',
            'module' => $module,
            'permission' => 'webhook manage'
        ]);
    }
}
