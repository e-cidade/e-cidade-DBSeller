<?php

namespace App\Notifications;

use App\Domain\Patrimonial\Contratos\Models\Acordo;
use App\Domain\Patrimonial\Contratos\Models\MensageriaAcordoDia;
use App\Mail\NotificacaoAcordoVencimento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class UserNotificationAcordoVencimento extends Notification implements ShouldQueue
{
     use Queueable, SerializesModels;

    public $mensageria_acordo_dias_id;
    public $acordo_id;
    public $title;
    public $message;
    public $instituicao_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $mensageria_acordo_dias_id,
        $acordo_id
    ) {
        $this->mensageria_acordo_dias_id = $mensageria_acordo_dias_id;
        $acordo = Acordo::find($acordo_id);
        $this->instituicao_id = $acordo->ac16_instit;
        $mensageria_acordo_dia = MensageriaAcordoDia::find($mensageria_acordo_dias_id);
        $mensageria_acordo = $mensageria_acordo_dia->mensageriaAcordo;

        $this->title = $this->replaceText($acordo, $mensageria_acordo_dia, $mensageria_acordo->ac51_assunto);
        $this->message = $this->replaceText($acordo, $mensageria_acordo_dia, $mensageria_acordo->ac51_mensagem);

        $this->title = \DBString::utf8_encode_all($this->title);
        $this->message = \DBString::utf8_encode_all($this->message);
    }


    private function replaceText(Acordo $acordo, MensageriaAcordoDia $mensageria_acordo_dia, $text)
    {
        $text = str_replace("[numero]", $acordo->ac16_numeroacordo, $text);

        $text = str_replace("[dias]", $mensageria_acordo_dia->dia, $text);

        $text = str_replace("[ano]", $acordo->ac16_anousu, $text);

        $text = str_replace("[data_inicial]", (new \DateTime($acordo->ac16_datainicio))->format('d/m/Y'), $text);

        return str_replace("[data_final]", (new \DateTime($acordo->ac16_datafim))->format('d/m/Y'), $text);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'message' => $this->message
        ];
    }
    public function toMail($notifiable)
    {
        return (new NotificacaoAcordoVencimento($this->instituicao_id, $notifiable->nome, $this->message))
            ->to($notifiable->email)
            ->subject($this->title);
    }
}
