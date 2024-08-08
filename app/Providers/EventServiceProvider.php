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
            $event->menu->add('MENU');

            $event->menu->add([
                'icon' => 'fas fa-home',
                'text' => 'Home',
                'route' => 'home.index',
            ]);
            $event->menu->add([
                'icon' => 'fas fa-users',
                'text' => 'New Users',
                'route' => 'users.index',
            ]);
            $event->menu->add([
                'icon' => 'fas fa-fw fa-building',
                'text' => 'Institutions',
                'route' => 'institutions.index',
            ]);
            $event->menu->add([
                'icon' => 'fas fa-book',
                'text' => 'Balances',
                'route' => 'users.index',
            ]);
            $event->menu->add([
                'icon' => 'fas fa-user-plus',
                'text' => 'Accounts',
                'route' => 'accounts.index',
            ]);
            // $event->menu->add([
            //     'icon' => 'fa fa-signal',
            //     'text' => 'Metrics',
            //     'route' => 'users.index',
            // ]);
            $event->menu->add('BLOCKCHAIN');
                        $event->menu->add([
                'icon' => 'fas fa-cubes',
                'text' => 'Network',
                'route' => 'blockchains.index',
            ]);
            $event->menu->add('PROFILE');

            $event->menu->add([
                'icon' => 'fas fa-cogs',
                'text' => 'My profile',
                'active' => ['dashboard/settings*'],
                'route' => 'settings.index',
            ]);
        });
    }
}
