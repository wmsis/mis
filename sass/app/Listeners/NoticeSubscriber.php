<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AnnouncementEvent;
use App\Events\TaskEvent; //事件
use App\Models\MIS\Notice;

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
            TaskEvent::class => 'handleTask',
        ];
    }

    /**
     * 处理发布公告事件 插入事件通知数据到数据库
     */
    public function handleAnnouncement($event) {
        //$this->saveAnnounceNoticeData($event); //有开启事件监听，这里不订阅
    }

    public function handleTask($event) {
        $this->saveTaskNoticeData($event);
    }

    private function saveAnnounceNoticeData($event){
        Notice::create([
            'user_id' => $event->user->id,
            'status' => 'init',
            'type' => 'announce',
            'foreign_id' => $event->announcement->id,
            'orgnization_id' => $event->announcement->orgnization_id
        ]);
    }

    private function saveTaskNoticeData($event){
        Notice::create([
            'user_id' => $event->user->id,
            'status' => 'init',
            'type' => 'task',
            'foreign_id' => $event->task->id,
            'orgnization_id' => $event->task->orgnization_id
        ]);
    }
}
