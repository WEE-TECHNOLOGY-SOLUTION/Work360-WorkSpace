<?php

namespace Workdo\SignInWithGoogle\Listeners;
use App\Events\SuperAdminSettingEvent;

class SuperAdminSettingListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingEvent $event): void
    {
        $module = 'SignInWithGoogle';
        if(in_array($module,$event->html->modules))
        {
            $methodName = 'index';
            $controllerClass = "Workdo\\SignInWithGoogle\\Http\\Controllers\\SuperAdmin\\SettingsController";
            if (class_exists($controllerClass)) {
                $controller = \App::make($controllerClass);
                if (method_exists($controller, $methodName)) {
                    $html = $event->html;
                    $settings = $html->getSettings();
                    $output =  $controller->{$methodName}($settings);
                    $html->add([
                        'html' => $output->toHtml(),
                        'order' => 660,
                        'module' => $module,
                        'permission' => 'google manage'
                    ]);
                }
            }
        }
    }
}
