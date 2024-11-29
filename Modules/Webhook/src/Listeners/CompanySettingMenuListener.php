<?php

namespace Workdo\Webhook\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
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
