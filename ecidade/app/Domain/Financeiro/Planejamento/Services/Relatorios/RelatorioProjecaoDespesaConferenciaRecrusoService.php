<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Relatorios\ConferenciaRecursoProjecaoDespesaPdf;
use App\Domain\Financeiro\Planejamento\Services\RelatorioProjecaoDespesaAgrupadaService;
use Illuminate\Support\Facades\DB;

class RelatorioProjecaoDespesaConferenciaRecrusoService extends RelatorioProjecaoDespesaAgrupadaService
{
    protected function processar(array $filtros)
    {
        $this->planejamento = Planejamento::find($filtros['planejamento_id']);
        $this->carregaExercicios();
        $this->agrupar = 'recurso_original';
        $this->buscarRecursos();
        $this->processaPlanejamento($filtros['instituicoes']);
        $this->organizaPlanejamento();
        $this->removeZerados();
    }

    public function emitirPdf()
    {
        $pdf = new ConferenciaRecursoProjecaoDespesaPdf();
        $pdf->setDados($this->dados);
        return $pdf->emitir();
    }

    protected function buscarRecursos()
    {
        $recursos = [];
        $fontesRecursos = DB::select(sprintf("
        select distinct o15_recurso, o15_descr, codigo_siconfi, o15_complemento, descricao
          from fonterecurso
          join orctiporec on o15_codigo = fonterecurso.orctiporec_id
         where exercicio = %s
           and codigo_siconfi != ''
         order by o15_recurso, codigo_siconfi, o15_complemento;
        ", $this->planejamento->pl2_ano_inicial));

        foreach ($fontesRecursos as $dadoFR) {
            $fonte = "{$dadoFR->codigo_siconfi}#{$dadoFR->o15_recurso}#{$dadoFR->o15_complemento}";
            $recursos[$fonte] = (object)[
                'codigo_original' => "{$dadoFR->o15_recurso}-{$dadoFR->o15_complemento}",
                'codigo_exercicio' => "{$dadoFR->codigo_siconfi}-{$dadoFR->o15_complemento}",
                'descricao_original' => $dadoFR->o15_descr,
                'descricao_exercicio' => $dadoFR->descricao,
                'valorBase' => 0,
                'exerciciosPlanejamento' => $this->criaArrayValores($this->planejamento->execiciosPlanejamento()),
            ];
        }

        $this->dados['dados'] = $recursos;
    }
}
