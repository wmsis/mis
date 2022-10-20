<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AnnouncementEvent;

class NoticeSubscriber
{
    /**
     * 事件订阅者
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 为订阅者注册侦听器
     *
     * @param  object  $event
     * @return void
     */
    public function subscribe($events)
    {
        // $events->listen(
        //     AnnouncementEvent::class, [NoticeSubscriber::class, 'handleAnnouncement']
        // );

        return [
            AnnouncementEvent::class => 'handleAnnouncement',
        ];
    }

    /**
     * 处理发布公告事件 插入事件通知数据到数据库
     */
    public function handleAnnouncement($event) {

    }

    private function saveNoticeData(){
        
    }
}
