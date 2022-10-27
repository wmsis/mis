<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AnnouncementEvent;
use App\Events\TaskEvent; //事件
use App\Events\AlarmEvent;
use App\Models\MIS\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmail;
use App\Models\MIS\Alarm;
use App\Models\MIS\AlarmRule;
use App\Models\SIS\Orgnization;
use Log;

class NoticeSubscriber
{
    protected $notice;

    /**
     * 事件订阅者
     *
     * @return void
     */
    public function __construct()
    {

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
            AlarmEvent::class => 'handleAlarm',
        ];
    }

    /**
     * 处理发布公告事件 插入事件通知数据到数据库
     */
    public function handleAnnouncement($event) {
        $this->saveAnnounceNoticeData($event); //也可以开启事件监听，这里不订阅  EventServiceProvider中listener需要配置
    }

    //处理任务事件 插入事件通知数据到数据库
    public function handleTask($event) {
        $this->saveTaskNoticeData($event);
    }

    //处理报警事件 插入事件通知数据到数据库
    public function handleAlarm($event) {
        Log::info('0000000000000');
        $this->saveAlarmNoticeData($event);
    }

    //插入公告通知数据到数据库
    private function saveAnnounceNoticeData($event){
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

        //发送邮件通知
        Notification::send($users, new SendEmail('announcement', $event->announcement));
    }

    //插入任务通知数据到数据库
    private function saveTaskNoticeData($event){
        $notice_obj = (new Notice())->setConnection($event->tenement_conn);
        $notice_obj->create([
            'user_id' => $event->user->id,
            'status' => 'init',
            'type' => 'task',
            'foreign_id' => $event->task->id,
            'orgnization_id' => $event->task->orgnization_id
        ]);

        //发送邮件通知
        Notification::send($event->user, new SendEmail('task', $event->task));
    }

    //插入报警通知数据到数据库
    private function saveAlarmNoticeData($event){
        Log::info('1111111111111111');
        Log::info($event->alarm->alarm_rule_id);
        $alarm_rule_obj = (new AlarmRule())->setConnection($event->tenement_conn);
        $alarm_rule = $alarm_rule_obj->where('id', $event->alarm->alarm_rule_id)->first();
        Log::info('AAAAAAAAAAAAAAAAA');
        Log::info(var_export($alarm_rule, true));
        if($alarm_rule){
            Log::info('2222222222222222222222');
            $event->alarm->alarm_rule = $alarm_rule;
            $notice_obj = (new Notice())->setConnection($event->tenement_conn);
            $user_obj = (new User())->setConnection($event->tenement_conn);
            Log::info('333333333333');
            Log::info($alarm_rule->notify_user_ids);
            $id_arr = explode(',', $alarm_rule->notify_user_ids);
            $users = $user_obj->whereIn('id', $id_arr)->get();
            foreach ($users as $k1 => $user) {
                $notice_obj->create([
                    'user_id' => $user->id,
                    'status' => 'init',
                    'type' => 'alarm',
                    'foreign_id' => $event->alarm->id,
                    'orgnization_id' => $event->alarm->orgnization_id
                ]);
            }

            //发送邮件通知
            Notification::send($users, new SendEmail('alarm', $event->alarm));
        }
    }
}
