<?php

namespace Workdo\Mollie\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'Mollie';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Mollie',
            'name' => 'mollie',
            'order' => 1060,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'mollie-sidenav',
            'module' => $module,
            'permission' => 'mollie payment manage'
        ]);
    }
}
