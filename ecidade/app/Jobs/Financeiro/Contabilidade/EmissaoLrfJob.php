<?php

namespace App\Jobs\Financeiro\Contabilidade;

use App\Domain\Financeiro\Contabilidade\Factories\VersoesRelatoriosLegaisMscFactory;
use App\Domain\Financeiro\Contabilidade\Models\EmissoesLrf;
use ECidade\Lib\Session\DatabaseSession;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmissaoLrfJob implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    private $queueService;

    private $filtros = [];

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function handle()
    {
        foreach ($this->filtros as $key => $value) {
            $_SESSION[$key] = $value;
        }

        DatabaseSession::getInstance()->addSessionToDatabase();

        $service = VersoesRelatoriosLegaisMscFactory::getService($this->filtros);
        $service->emitirComUpload();
    }

    public function failed(\Exception $e)
    {
        $emissao = EmissoesLrf::find($this->filtros['id_emissao']);
        $emissao->c181_status = 'ERRO';
        $emissao->save();
    }
}
