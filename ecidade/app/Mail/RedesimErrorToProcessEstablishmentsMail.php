<?php

namespace App\Mail;

use App\Domain\Configuracao\Instituicao\Repository\InstituicaoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RedesimErrorToProcessEstablishmentsMail extends Mailable
{
    use Queueable, SerializesModels;

    private $establishmentIdentifier;

    private $exception;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($establishmentIdentifier, $exception)
    {
        $this->establishmentIdentifier = $establishmentIdentifier;
        $this->exception = $exception;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        if (!env("REDESIM_MAIL_ADDRESS_ON_ERROR") || !env("MAIL_FROM_ADDRESS")) {
            throw new \Exception("E-mail não pode ser enviado pois não foi feita a configuração.");
        }

        $instituicaoRepository = new InstituicaoRepository();
        $instituicao = $instituicaoRepository->find(1);

        $this->to(explode(",", env("REDESIM_MAIL_ADDRESS_ON_ERROR")));
        $this->subject("Erro no processamento automatico da REDESIM: {$instituicao->getNome()}");
        $this->from(env("MAIL_FROM_ADDRESS"), $instituicao->getNome());

        $errorMessage = "Mensagem: {$this->exception->getMessage()}<br>";
        $errorMessage .= "Arquivo: {$this->exception->getFile()}<br>";
        $errorMessage .= "Linha: {$this->exception->getLine()}";

        return $this->view('emails.redesim.errorToProcessEstablishments')->with([
            'establishmentIdentifier' => $this->establishmentIdentifier,
            'errorMessage' => $errorMessage,
            'exceptionStackTrace' => str_replace("\n", "<br>", $this->exception->getTraceAsString())
        ]);
    }
}
