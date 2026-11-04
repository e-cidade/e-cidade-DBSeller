<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SolicitacaoAssinaturaNotification extends Notification
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
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $menuName;

    /**
     * Create a new notification instance.
     * @param string $title
     * @param string $message
     * @param string $action
     * @return void
     */
    public function __construct($title, $message, $action)
    {
        $this->menuName = \DBString::utf8_encode_all('Assinatura Documentos');
        $this->title = $title;
        $this->message = $message;
        $this->action = (object)[
            "action" => $action,
            "iInstitId" => 1,
            "iAreaId" => 4,
            "iModuloId" => 604,
            "lAtalhoDesktop" => true
        ];
    }

    /**
     * Get the notification's delivery channels.
     * @param mixed $notifiable
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
            'message' => $this->message,
            'action' => $this->action,
            'menuName' => $this->menuName
        ];
    }
}
