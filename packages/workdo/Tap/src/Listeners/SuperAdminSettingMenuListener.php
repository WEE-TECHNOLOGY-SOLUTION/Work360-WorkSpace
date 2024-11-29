<?php

namespace Workdo\Tap\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'Tap';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Tap'),
            'name' => 'tap',
            'order' => 1240,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'tap-sidenav',
            'module' => $module,
            'permission' => 'tap manage'
        ]);
    }
}
