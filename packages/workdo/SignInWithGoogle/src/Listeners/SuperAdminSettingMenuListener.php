<?php

namespace Workdo\SignInWithGoogle\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'SignInWithGoogle';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Google Setting',
            'name' => 'signinwithgoogle',
            'order' => 660,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'home',
            'navigation' => 'googlesignin_setting',
            'module' => $module,
            'permission' => 'google manage'
        ]);
    }
}
