<?php

namespace Workdo\Mollie\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
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
