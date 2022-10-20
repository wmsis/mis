<?php

namespace App\Listeners;

use App\Events\AnnouncementEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnnouncementNotification implements ShouldQueue   //事件监听器队列
{
    /**
     * 事件监听器
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 处理事件  插入事件通知数据到数据库
     *
     * @param  \App\Events\AnnouncementEvent  $event
     * @return void
     */
    public function handle(AnnouncementEvent $event)
    {
        //
    }
}
