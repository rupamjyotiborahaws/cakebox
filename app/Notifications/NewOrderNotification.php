<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class NewOrderNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $message;
    public $order_no;

    /**
     * Create a new notification instance.
     */
    public function __construct($message,$order_no)
    {
        $this->message = $message;
        $this->order_no = $order_no;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'order_no' => $this->order_no,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'order_no' => $this->order_no
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin-notifications');
    }
}
