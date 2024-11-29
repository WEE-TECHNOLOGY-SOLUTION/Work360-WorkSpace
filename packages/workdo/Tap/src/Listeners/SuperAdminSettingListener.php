<?php

namespace Workdo\Tap\Listeners;
use App\Events\SuperAdminSettingEvent;

class SuperAdminSettingListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingEvent $event): void
    {
        $module = 'Tap';
        $methodName = 'index';
        $controllerClass = "Workdo\\Tap\\Http\\Controllers\\SuperAdmin\\SettingsController";

        if (class_exists($controllerClass)) {
            $controller = \App::make($controllerClass);

            if (method_exists($controller, $methodName)) {

                $html = $event->html;
                $settings = $html->getSettings();
                $output =  $controller->{$methodName}($settings);

                $html->add([
                    'html' => $output->toHtml(),
                    'order' => 1240,
                    'module' => $module,
                    'permission' => 'tap manage'
                ]);
            }
        }
    }
}
