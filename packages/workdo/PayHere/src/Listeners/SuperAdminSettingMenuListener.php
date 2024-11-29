<?php

namespace  Workdo\PayHere\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'PayHere';
        $menu = $event->menu;
        $menu->add([
            'title' => __('PayHere'),
            'name' => 'payhere',
            'order' => 1280,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'payhere-sidenav',
            'module' => $module,
            'permission' => 'payhere manage'
        ]);
    }
}
