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

class AnnouncementEvent implements ShouldBroadcast
{
    public $announcement;
    public $user;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Announcement $announcement)
    {
        $this->user = $user;
        unset($announcement->notify_user_ids);
        unset($announcement->orgnization_id);
        $this->announcement = $announcement;
    }

    /**
     * 发布事件广播到前端用户
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    //广播内容
    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'announcement' => $this->announcement
        ];
    }
}
