<?php

namespace Workdo\Tap\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
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
