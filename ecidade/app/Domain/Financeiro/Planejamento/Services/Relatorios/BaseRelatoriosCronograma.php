<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Orcamento\Services\Relatorios\BaseCronograma;
use App\Domain\Financeiro\Planejamento\Models\Comissao;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;

abstract class BaseRelatoriosCronograma extends BaseCronograma
{
    /**
     * @var Planejamento
     */
    protected $planejamento;

    protected $ementario = 'ecidade';

    /**
     * @param array $filtros
     */
    public function __construct(array $filtros)
    {
        $this->processaFiltros($filtros);
    }

    protected function processaFiltros(array $filtros)
    {
        $this->getPlanejamento($filtros['planejamento_id']);
        $this->exercicio = (int)$filtros['exercicio'];
        $this->agruparPor = $filtros['agruparPor'];
        $this->periodicidade = $filtros['periodicidade'];
        $this->instituicoes = $filtros['instituicoes'];
        if (!empty($filtros['ementario'])) {
            $this->ementario = $filtros['ementario'];
        }

        $this->organizaFiltrosEmissao();
        $this->inicializaTotalizadores();
    }

    /**
     * Organiza os filtros de emissão
     */
    protected function organizaFiltrosEmissao()
    {
        $this->organizaFiltrosComissao();
        $this->organizaFiltrosPlanejamento();

        $this->dados['filtros']['exercicio'] = $this->exercicio;
        $this->dados['filtros']['agruparPor'] = $this->agruparPor;
        $this->dados['filtros']['periodicidade'] = $this->periodicidade;
        $this->dados['ementario'] = $this->ementario;
    }

    protected function getPlanejamento($planejamento_id)
    {
        $this->planejamento = Planejamento::find($planejamento_id);
    }

    /**
     * Organiza os filtros do planejamento
     */
    protected function organizaFiltrosPlanejamento()
    {
        $this->dados['planejamento'] = $this->planejamento->toArray();
        $this->dados['planejamento']['exercicios'] = $this->planejamento->execiciosPlanejamento();
        $this->dados['planejamento']['missao'] = $this->planejamento->pl2_missao;
        $this->dados['planejamento']['visao'] = $this->planejamento->pl2_visao;
        $this->dados['planejamento']['valores'] = $this->planejamento->pl2_valores;
    }

    /**
     * Organiza os filtros da comissão do planejamento
     */
    protected function organizaFiltrosComissao()
    {
        $cgms = $this->planejamento->comissoes->map(function (Comissao $comissao) {
            return $comissao->cgm->z01_nome;
        })->toArray();
        $this->dados['planejamento']['comissao'] = $cgms;
    }

    /**
     * Retorna o exercício do mapeamento a ser usado.
     * Nem sempre teremos o ementário para o exercício do planejamento, nesse caso ficou acordado de pegar o anterior.
     * @return int
     */
    protected function exercicioMapeamento()
    {
        $plano = PlanoReceita::query()
            ->where('uniao', $this->ementario === 'uniao')
            ->where('exercicio', $this->planejamento->pl2_ano_inicial)
            ->first();

        if (is_null($plano)) {
            return $this->planejamento->pl2_ano_inicial - 1;
        }

        return $this->planejamento->pl2_ano_inicial;
    }
}
