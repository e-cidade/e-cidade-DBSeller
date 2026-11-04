<?php

namespace App\Mail;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Helpers\Imagem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacaoAcordoVencimento extends Mailable
{
    use Queueable, SerializesModels;

    public $logoBase64;
    public $nome;
    public $mensagem;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($instituicao_id, $nome, $mensagem_text)
    {
        $this->nome = $nome;
        $this->mensagem = $mensagem_text;
        $instituicao = DBConfig::find($instituicao_id);
        $caminhoLogo = ECIDADE_PATH . 'imagens/files/' . $instituicao->logo;
        $this->logoBase64 =  Imagem::caminhoArquivoParaBase64($caminhoLogo);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.acordo.notificar-acordo-vencido')
            ->with([
                'logo' => $this->logoBase64,
                'nome' => $this->nome,
                'mensagem' => $this->mensagem
            ]);
    }
}
