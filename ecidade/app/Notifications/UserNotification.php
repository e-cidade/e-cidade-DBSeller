<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param string $title
     * @param string $message
     * @return void
     */
    public function __construct($title, $message)
    {
        $this->title = \DBString::utf8_encode_all($title);
        $this->message = \DBString::utf8_encode_all($message);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'message' => $this->message
        ];
    }
}
