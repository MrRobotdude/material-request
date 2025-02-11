<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // if (app()->environment('local')) {
        //     URL::forceScheme('https');
        // }

        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $user = Auth::user();

            if ($user) {
                $event->menu->add([
                    'key' => 'change_password',
                    'text' => 'Change Password',
                    'url' => route('account-management.change-password', ['user' => $user->user_id]),
                    'icon' => 'fas fa-fw fa-lock'
                ]);
            }
        });

    }
}
