<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MIS\Alarm;
use App\Models\User;

class AlarmEvent
{
    public $alarm;
    public $user;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Announcement $alarm)
    {
        $this->user = $user;
        $this->alarm = $alarm;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('alarm.' . $this->user->id);
    }

    //广播内容
    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'alarm' => $this->alarm
        ];
    }
}
