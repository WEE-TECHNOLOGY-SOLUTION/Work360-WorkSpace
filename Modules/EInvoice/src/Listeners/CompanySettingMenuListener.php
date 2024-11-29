<?php

namespace Workdo\EInvoice\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'EInvoice';
        $menu = $event->menu;
        $menu->add([
            'title' => __('E-invoice Setting'),
            'name' => 'e-invoice setting',
            'order' => 710,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation'=>'e-invoice-sidenav',
            'module' => $module,
            'permission' => 'einvoice manage'
        ]);
    }
}
