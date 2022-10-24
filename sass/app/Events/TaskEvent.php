<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MIS\Task;
use App\Models\User;

class TaskEvent implements ShouldBroadcast
{
    public $task;
    public $user;
    public $tenement_conn;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 创建任务信息时触发事件，此处一个任务为一个用户，可以继承ShouldBroadcast，为用户触发一个事件通知，通过频道发出
     *
     * @return void
     */
    public function __construct(User $user, Task $task, $tenement_conn)
    {
        $this->user = $user;
        $this->task = $task;
        $this->tenement_conn = $tenement_conn;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('task.' . $this->user->id);
    }

    //广播内容
    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'task' => $this->task
        ];
    }
}
