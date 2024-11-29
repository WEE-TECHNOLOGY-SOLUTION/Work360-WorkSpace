<?php

namespace Workdo\SignInWithGoogle\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\SignInWithGoogle\Providers\EventServiceProvider;
use Workdo\SignInWithGoogle\Providers\RouteServiceProvider;
use Workdo\SignInWithGoogle\Providers\ViewComposer;

class SignInWithGoogleServiceProvider extends ServiceProvider
{

    protected $moduleName = 'SignInWithGoogle';
    protected $moduleNameLower = 'signinwithgoogle';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(ViewComposer::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'sign-in-with-google');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerTranslations();
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(__DIR__.'/../Resources/lang');
        }
    }
}
