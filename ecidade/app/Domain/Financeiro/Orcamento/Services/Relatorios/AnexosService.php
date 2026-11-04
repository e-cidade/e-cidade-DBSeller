<?php

namespace App\Domain\Financeiro\Orcamento\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Orcamento\Models\FonteReceita;

abstract class AnexosService
{
    /**
     * @var integer
     */
    protected $exercicio;
    /**
     * @var DBConfig[]
     */
    protected $instituicoes;

    /**
     * @var array
     */
    protected $codigosInstituicoes;
    /**
     * @var array
     */
    protected $nomesInstituicoes;
    /**
     * Valores possíveis
     *  - ecidade
     *  - uniao
     *  - estadual
     * @var string
     * /**
     * @var PlanoReceita[]|FonteReceita[]
     */
    protected $fontesReceitas;

    /**
     * Filtros aplicados na emissão
     * @var array
     */
    protected $filtros = [];

    /**
     * Aceita os seguintes valores:
     * - "orcamento" considera apenas o saldo inicial
     * - "balanco" dados conforme emissão do balancete para o período
     * @var string
     */
    protected $dadosEmissao = 'balanco';

    /**
     * @var array
     */
    protected $titulosRelatorio = [];
    /**
     * @var DBConfig
     */
    protected $instituicaoEmissora;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
        $this->exercicio = (int)$filtros['DB_anousu'];
        $this->codigosInstituicoes = $filtros['instituicoes'];
        $this->ementario = $filtros['ementario'];

        if (isset($filtros['dadosEmissao'])) {
            $this->dadosEmissao = $filtros['dadosEmissao'];
        }

        $this->instituicoes = DBConfig::query()->whereIn('codigo', $filtros['instituicoes'])
            ->orderBy('codigo')
            ->get();

        $this->instituicaoEmissora = $this->instituicoes->filter(function (DBConfig $instituicao) {
            return $instituicao->codigo === (int)$_SESSION['DB_instit'];
        })->shift();

        $this->instituicoes->each(function (DBConfig $instituicao) {
            $this->nomesInstituicoes[] = "{$instituicao->codigo} - {$instituicao->nomeinst}";
        });

        $this->titulosRelatorio = $this->processaTitulosRelatorios();
    }

    /**
     * @return string
     */
    protected function getOrigemEmentario()
    {
        $origemEmentario = 'Ementário do e-Cidade';
        if ($this->ementario === 'uniao') {
            $origemEmentario = 'Ementário da união/federação';
        }
        if ($this->ementario === 'estadual') {
            $origemEmentario = 'Ementário estadual/regional';
        }
        return $origemEmentario;
    }

    abstract protected function processaTitulosRelatorios();
}
