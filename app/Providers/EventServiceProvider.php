<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add('MENU PRINCIPAL');

            $event->menu->add([
                'icon' => 'fas fa-home',
                'text' => 'Início',
                'route' => 'home.index',
            ]);

            $event->menu->add('PERFIL');

            $event->menu->add([
                'icon' => 'user',
                'text' => 'Meu perfil',
                'active' => ['dashboard/settings*'],
                'route' => 'settings.index',
            ]);
        });
    }
}
