<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\AnnouncementEvent; //事件
use App\Listeners\AnnouncementNotification; //事件监听器
use App\Listeners\NoticeSubscriber;  //订阅者类

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        //手动注册事件
        AnnouncementEvent::class => [
            AnnouncementNotification::class,
        ],
    ];

    /**
     * 被注册的订阅者类
     *
     * @var array
     */
    protected $subscribe = [
        NoticeSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
