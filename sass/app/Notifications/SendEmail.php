<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmail extends Notification implements ShouldQueue
{
    protected $instance;
    protected $type;
    protected $title;
    protected $content;
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $instance)
    {
        $this->instance = $instance;
        $this->type = $type;
        if($this->type == 'announcement'){
            $this->title = $this->instance->title;
            $this->content = $this->instance->content;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
}