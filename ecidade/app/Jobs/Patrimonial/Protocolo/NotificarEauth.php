<?php

namespace App\Jobs\Patrimonial\Protocolo;

use App\Domain\Patrimonial\Protocolo\Services\NoticarAssinanteDocumentoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificarEauth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cpfCnpj;
    public $nome;
    public $email;
    public $processoDocumento;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cpfCnpj, $nome, $email, $processoDocumento)
    {
        $this->cpfCnpj = $cpfCnpj;
        $this->nome = $nome;
        $this->email = $email;
        $this->processoDocumento = $processoDocumento;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new NoticarAssinanteDocumentoService();
        $service->notificaAssinatura(
            $this->cpfCnpj,
            $this->nome,
            $this->email,
            $this->processoDocumento
        );
    }
}
