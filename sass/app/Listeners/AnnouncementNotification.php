<?php

namespace App\Listeners;

use App\Events\AnnouncementEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\MIS\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmail;

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
        $this->saveNoticeData($event);
    }

    //插入事件通知数据到数据库
    private function saveNoticeData($event){
        $user_obj = (new User())->setConnection($event->tenement_conn);
        $id_arr = explode(',', $event->announcement->notify_user_ids);
        $users = $user_obj->whereIn('id', $id_arr)->get();

        $notice_obj = (new Notice())->setConnection($event->tenement_conn);
        foreach ($users as $key => $user) {
            $notice_obj->create([
                'user_id' => $user->id,
                'status' => 'init',
                'type' => 'announce',
                'foreign_id' => $event->announcement->id,
                'orgnization_id' => $event->announcement->orgnization_id
            ]);
        }

        //发送邮件通知和频道通知
        Notification::send($users, new SendEmail('announcement', $event->announcement));
    }
}
