<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

use App\Domain\Financeiro\Contabilidade\Factories\VersoesRelatoriosLegaisMscFactory;
use App\Domain\Financeiro\Contabilidade\Models\EmissoesLrf;
use App\Jobs\Financeiro\Contabilidade\EmissaoLrfJob;

class EmiteControleVersaoService
{

    protected $filtros = [];

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function execute()
    {
        $this->filtros['id_emissao'] = $this->criaEmissao();
        dispatch(new EmissaoLrfJob($this->filtros)); // por job
//        // para teste sincrono
//        $service = VersoesRelatoriosLegaisMscFactory::getService($this->filtros);
//        $service->emitirComUpload();
    }

    private function criaEmissao()
    {
        $model = new EmissoesLrf();
        $model->c181_relatorio = $this->filtros['relatorio'];
        $model->c181_periodo = $this->filtros['periodo'];
        $model->c181_usuario = $this->filtros['DB_id_usuario'];
        $model->c181_instituicao = $this->filtros['DB_instit'];
        $model->c181_filtrosemissao = \JSON::create()->stringify($this->filtros);
        $model->save();
        return $model->c181_codigo;
    }
}
