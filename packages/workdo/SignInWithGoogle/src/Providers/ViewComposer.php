<?php

namespace Workdo\SignInWithGoogle\Providers;
use App\Facades\ModuleFacade as Module;
use Illuminate\Support\ServiceProvider;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['auth.login', 'auth.register'], function ($view) {
            $settings = getAdminAllSetting();

            if (Module::isEnabled('SignInWithGoogle') && isset($settings['google_signin_setting_enabled']) && $settings['google_signin_setting_enabled'] == 'on' && !empty($settings['google_client_id']) && !empty($settings['google_client_secret_key']) && !empty($settings['google_authorized_url'])) {
                $view->getFactory()->startPush('SigninButton', view('sign-in-with-google::button', compact('settings')));
            }
        });
    }

    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
