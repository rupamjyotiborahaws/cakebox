<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Support\Facades\Log;

class NewOrderNotification extends Notification //implements ShouldBroadcast
{
    use Queueable;

    //public $message;
    //public $order_no;

    /**
     * Create a new notification instance.
     */
    // public function __construct()
    // {
    //     //$this->message = $message;
    //     //$this->order_no = $order_no;
    // }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        //return ['broadcast', 'database']; Websocket notification
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        Log::info('Sending push notification to user ID: ' . $notifiable->id);
        return (new WebPushMessage)
            ->title('Hey, You got a new order at')
            ->icon('/images/notification-icon.png')
            ->body('Visit the admin portal')
            ->action('View App', 'view_app')
            ->data(['url' => '/admin/dashboard']);
    }

    // public function toBroadcast(object $notifiable): BroadcastMessage
    // {
    //     return new BroadcastMessage([
    //         'message' => $this->message,
    //         'order_no' => $this->order_no,
    //     ]);
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    // public function toDatabase(object $notifiable): array
    // {
    //     return [
    //         'message' => $this->message,
    //         'order_no' => $this->order_no
    //     ];
    // }

    // public function broadcastOn()
    // {
    //     return new PrivateChannel('admin-notifications');
    // }
}
