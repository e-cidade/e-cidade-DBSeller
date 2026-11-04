<?php

namespace App\Jobs\Patrimonial\Protocolo;

use App\Domain\Patrimonial\Protocolo\Services\NotificarTransferenciaProcessoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificarTransferenciaProcessoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $idUsuario;
    public $mensagem;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($idUsuario, $mensagem)
    {
        $this->idUsuario = $idUsuario;
        $this->mensagem = $mensagem;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new NotificarTransferenciaProcessoService();
        $service->notificarTransferencia(
            $this->idUsuario,
            $this->mensagem
        );
    }
}
