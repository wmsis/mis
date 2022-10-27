<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SendEmail extends Notification implements ShouldQueue
{
    protected $instance;
    protected $msgtype;
    protected $title;
    protected $content;
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($msgtype, $instance)
    {
        $this->instance = $instance;
        $this->msgtype = $msgtype;
        if($this->msgtype == 'announcement'){
            $this->title = $this->instance->title;
            $this->content = $this->instance->content;
        }
        elseif($this->msgtype == 'alarm'){
            $this->title = $this->instance->alarm_rule->name;
            $this->content = $this->instance->content;
        }
        elseif($this->msgtype == 'task'){
            $this->title = $this->instance->name;
            $this->content = $this->instance->content;
        }
    }

    /**
     * 发送指定频道.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     * 发送通知时，通知系统将自动在您的应通知实体上查找 email 属性
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('伟明环保设备有限公司')
                    ->greeting($this->title)    //问候语
                    ->line($this->content)      //一行文本
                    ->action('去看看', url('/')) //一个按钮超链接
                    ->line('感谢您的信赖！');    //一行文本
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    //广播通知
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'msgtype' => $this->msgtype,
            'instance' => $this->instance
        ]);
    }
}
