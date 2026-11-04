<?php


namespace App\Domain\Financeiro\Contabilidade\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class BalanceteDespesaService
 * Responsável por montar a busca dos dados para o balancete da despesa
 *
 * @package App\Domain\Financeiro\Contabilidade\Services
 */
class BalanceteDespesaService
{
    /**
     * árvore criada com os filtros (aquela aba maldita)
     * @var stdClass
     */
    protected $filtro;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $filtrarInstituicoes;

    /**
     * @var Carbon
     */
    protected $filtroDataInicio;
    /**
     * @var Carbon
     */
    protected $filtroDataFinal;

    protected $ano;


    /**
     * Valores aceitos:
     * - orgao
     * - unidade
     * - funcao
     * - subfuncao
     * - programa
     * - projeto
     * - elemento
     * - recurso
     * @var array
     */
    protected $agruparPorClassificacao = [];

    /**
     * Lista de agrupadores
     * @var string[]
     */
    protected $agrupadores = [
        'orgao' => [
            'orgao',
            'descricao_orgao'
        ],
        'unidade' => [
            'orgao',
            'descricao_orgao',
            'unidade',
            'descricao_unidade'
        ],
        'funcao' => [
            'funcao',
            'descricao_funcao'
        ],
        'subfuncao' => [
            'subfuncao',
            'descricao_subfuncao'
        ],
        'programa' => [
            'programa',
            'descricao_programa'
        ],
        'projeto' => [
            'projeto',
            'descricao_projeto'
        ],
        'elemento' => [
            'elemento',
            'descricao_elemento',
        ],
        'recurso' => [
            'recurso',
            'fonte_recurso',
            'descricao_recurso',
            'complemento',
            'descricao_complemento',
        ],
    ];
    /**
     * Array com uma lista de estruturais a filtrar
     * @var array
     */
    private $filtrarEstruturais = [];

    /**
     * Vem da aba de filtros da despesa
     * @param stdClass|null $filtro
     * @return BalanceteDespesaService
     */
    public function setFiltro($filtro)
    {
        $this->filtro = $filtro;
        return $this;
    }

    /**
     * @param \Illuminate\Support\Collection $filtrarInstituicoes
     * @return BalanceteDespesaService
     */
    public function setFiltrarInstituicoes($filtrarInstituicoes)
    {
        $this->filtrarInstituicoes = $filtrarInstituicoes;
        return $this;
    }

    /**
     * @param Carbon $filtroDataInicio
     * @return BalanceteDespesaService
     */
    public function setFiltroDataInicio($filtroDataInicio)
    {
        $this->filtroDataInicio = $filtroDataInicio;
        return $this;
    }

    /**
     * @param Carbon $filtroDataFinal
     * @return BalanceteDespesaService
     */
    public function setFiltroDataFinal($filtroDataFinal)
    {
        $this->filtroDataFinal = $filtroDataFinal;
        return $this;
    }

    /**
     * @param mixed $ano
     * @return BalanceteDespesaService
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
        return $this;
    }

    /**
     * @param array $agruparPorClassificacao
     * @return BalanceteDespesaService
     */
    public function setAgruparPorClassificacao($agruparPorClassificacao)
    {
        $this->agruparPorClassificacao = $agruparPorClassificacao;
        return $this;
    }

    /**
     * @param $sql
     * @return array
     */
    public function execute($sql)
    {
        return DB::select($sql);
    }

    /**
     * @return string
     */
    public function sqlPrincipal()
    {
        list($where, $dataInicio, $dataFinal) = $this->montaWhere();
        return "
            select fc_saldo_dotacao.*,
                   o58_orgao as orgao,
                   trim(o40_descr) as descricao_orgao,
                   o58_unidade as unidade,
                   trim(o41_descr) as descricao_unidade,
                   o58_funcao as funcao,
                   trim(o52_descr) as descricao_funcao,
                   o58_subfuncao as subfuncao,
                   trim(o53_descr) as descricao_subfuncao,
                   o58_programa as programa,
                   trim(o54_descr) as descricao_programa,
                   o58_projativ as projeto,
                   trim(o55_descr) as descricao_projeto,
                   o56_elemento as elemento,
                   trim(o56_descr) as descricao_elemento,
                   o15_recurso as fonte_recurso,
                   gestao,
                   codigo_siconfi as siconfi,
                   trim(descricao) as descricao_recurso,
                   o200_descricao as descricao_complemento,
                   o58_localizadorgastos as localizador_gasto,
                   o11_descricao as descricao_localizador_gasto,
                   c58_descr as caracteristica_peculiar,
                   nomeinst as nome_instituicao,
                   o58_codele
             from orcdotacao
             join fc_saldo_dotacao(o58_anousu, o58_coddot, '{$dataInicio}', '{$dataFinal}') on o58_coddot = reduzido
                  and o58_anousu = ano
             join orcorgao on (o40_anousu, o40_orgao) = (o58_anousu, o58_orgao)
             join orcunidade on (o41_anousu, o41_orgao, o41_unidade) = (o58_anousu, o58_orgao, o58_unidade)
             join orcprograma on (o54_anousu, o54_programa) = (o58_anousu, o58_programa)
             join orcprojativ on (o55_anousu, o55_projativ) = (o58_anousu, o58_projativ)
             join orcelemento on (o56_codele, o56_anousu) = (o58_codele, o58_anousu)
             join orctiporec on orctiporec.o15_codigo = recurso
             join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                  and fonterecurso.exercicio = ano
             join complementofonterecurso on complementofonterecurso.o200_sequencial = complemento
             join concarpeculiar on (c58_sequencial)= (o58_concarpeculiar)
             join orcfuncao on (o52_funcao) = (o58_funcao)
             join db_config on (codigo) = (o58_instit)
             join ppasubtitulolocalizadorgasto on (o11_sequencial) = (o58_localizadorgastos)
             join orcsubfuncao on (o53_subfuncao)   = (o58_subfuncao)
            where {$where}
            order by
             principal desc, orgao, unidade, funcao, subfuncao, programa, projeto, elemento, fonte_recurso, reduzido
        ";
    }

    public function sqlSintetico()
    {
        return $this->aplicarAgrupadores($this->sqlPrincipal());
    }

    protected function montaWhere()
    {
        $instituicoes = $this->filtrarInstituicoes->implode(',');

        $where = [
            "o58_anousu = {$this->ano}",
            "o58_instit in ($instituicoes)",
        ];

        if (!empty($this->filtrarEstruturais)) {
            $listaEstruturais = implode("', '", $this->filtrarEstruturais);
            $where[] = "o56_elemento in ('{$listaEstruturais}')";
        }

        $where = array_merge($where, filtrosDespesa($this->filtro));
        $dataInicio = $this->filtroDataInicio->format('Y-m-d');
        $dataFinal = $this->filtroDataFinal->format('Y-m-d');
        $where = implode(' and ', $where);
        return array($where, $dataInicio, $dataFinal);
    }

    /**
     *
     * @param $sql
     * @return string
     */
    protected function aplicarAgrupadores($sql)
    {
        $campos = [
            'sum(saldo_inicial)               as saldo_inicial',
            'sum(saldo_anterior)              as saldo_anterior',
            'sum(saldo_disponivel)            as saldo_disponivel',
            'sum(suplementado)                as suplementado',
            'sum(suplementado_especial)       as suplementado_especial',
            'sum(reducoes)                    as reducoes',
            'sum(empenhado)                   as empenhado',
            'sum(empenhado_liquido)           as empenhado_liquido',
            'sum(anulado)                     as anulado',
            'sum(liquidado)                   as liquidado',
            'sum(pago)                        as pago',
            'sum(empenhado_acumulado)         as empenhado_acumulado',
            'sum(empenhado_liquido_acumulado) as empenhado_liquido_acumulado',
            'sum(anulado_acumulado)           as anulado_acumulado',
            'sum(liquidado_acumulado)         as liquidado_acumulado',
            'sum(pago_acumulado)              as pago_acumulado',
            'sum(a_liquidar)                  as a_liquidar',
            'sum(a_pagar)                     as a_pagar',
            'sum(a_pagar_liquidado)           as a_pagar_liquidado',
        ];

        $camposAgrupadores = [];
        foreach ($this->agruparPorClassificacao as $classificacao) {
            $camposAgrupadores = array_merge($camposAgrupadores, $this->agrupadores[$classificacao]);
        }
        $camposRecursos = [
            'recurso',
            'fonte_recurso',
            'gestao',
            'siconfi',
            'descricao_recurso',
            'complemento',
            'descricao_complemento',
        ];
        $camposAgrupadores = array_unique(array_merge($camposAgrupadores, $camposRecursos));

        $campos = array_merge($campos, $camposAgrupadores);
        $campos = implode(', ', $campos);

        $group = implode(', ', $camposAgrupadores);

        unset($camposAgrupadores[array_search('recurso', $camposAgrupadores)]);
        $ordem = implode(', ', $camposAgrupadores);

        return "
            select {$campos}
              from ($sql) as x
            group by {$group}
            order by {$ordem}
        ";
    }

    public function filtrarEstruturais(array $filtroDespesa)
    {
        $this->filtrarEstruturais = $filtroDespesa;
        return $this;
    }
}
