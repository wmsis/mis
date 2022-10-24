<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MIS\Announcement;

class AnnouncementEvent
{
    public $announcement;
    public $tenement_conn;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 此事件通过事件监听器监听，没有通过事件 订阅器订阅
     * 创建公告信息时触发事件，公告发送的是多用户，此处不发送事件通知  不继承ShouldBroadcast
     *
     * @return void
     */
    public function __construct(Announcement $announcement, $tenement_conn)
    {
        $this->announcement = $announcement;
        $this->tenement_conn = $tenement_conn;
    }
}
