<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Repository;

use App\Domain\Core\Base\Repository\BaseRepository;
use Illuminate\Support\Facades\DB;

class CertidaoTempoContribuicaoRepository extends BaseRepository
{
    const SALARIO = 'salario';
    const COMPLEMENTAR = 'complementar';
    const RESCISAO = 'rescisao';
    const DECIMO = 'decimo';

    /**
     * @var string
     */
    private $periodoInicial;
    
    /**
     * @var string
     */
    private $periodoFinal;

    /**
     * @var int
     */
    private $codigoInstituicao;

    /**
     * @var int
     */
    private $ano;

    /**
     * @var int
     */
    private $mes;

    /**
     * @var int
     */
    private $matricula;

    /**
     * dados processados para impress�o
     *
     * @var array
     */
    private $dados = [];

    /**
     * @var array
     */
    private $tabelaPrevidencia = [];

    /**
     * @var array
     */
    private $tabelas = [
        self::SALARIO => "gerfsal",
        self::COMPLEMENTAR => "gerfcom",
        self::RESCISAO => "gerfres",
        self::DECIMO => "gerfs13",
    ];

    /**
     * @var int
     */
    private $anoCompetenciaInicio = "";
    /**
     * @var int
     */
    private $mesCompetenciaInicio = "";

    /**
     * @var int
     */
    private $anoCompetenciaFim = "";
    /**
     * @var int
     */
    private $mesCompetenciaFim = "";
    


    /**
     * @var array
     */
    private $siglas = [
        self::SALARIO => "r14",
        self::COMPLEMENTAR => "r48",
        self::RESCISAO => "r20",
        self::DECIMO => "r35",
    ];

    const SCHEMA = 'pessoal';

    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    public function setPeriodoInicial($periodoInicial)
    {
        $this->periodoInicial = $periodoInicial;
    }

    public function setPeriodoFinal($periodoFinal)
    {
        $this->periodoFinal = $periodoFinal;
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    public function setTabelaPrevidencia($tabelaPrevidencia)
    {
        $this->tabelaPrevidencia = $tabelaPrevidencia;
    }
    
    public function getDados()
    {
        if (!empty($this->periodoInicial)) {
            $dadosPeriodoInicial = explode("-", $this->periodoInicial);
            $this->anoCompetenciaInicio = $dadosPeriodoInicial[0];
            $this->mesCompetenciaInicio = $dadosPeriodoInicial[1];
        }

        if (!empty($this->periodoFinal)) {
            $dadosPeriodoFinal = explode("-", $this->periodoFinal);
            $this->anoCompetenciaFim = $dadosPeriodoFinal[0];
            $this->mesCompetenciaFim = $dadosPeriodoFinal[1];
        }

        $salario = $this->builderQuery(self::SALARIO);
        $complementar = $this->builderQuery(self::COMPLEMENTAR);
        $rescisao = $this->builderQuery(self::RESCISAO);
        $decimo = $this->builderQuery(self::DECIMO);
        $salario->unionAll($complementar);
        $salario->unionAll($rescisao);
        $salario->unionAll($decimo);
        $retorno = DB::table(
            DB::raw(
                "(
                    {$salario->toSql()}
                ) as x"
            )
        )
        ->select(
            DB::raw("rh02_regist AS matricula"),
            DB::raw("rh02_anousu AS ano"),
            DB::raw("rh02_mesusu AS mes"),
            DB::raw("sum(valor) AS total")
        )
        ->groupBy('matricula')
        ->groupBy('ano')
        ->groupBy('mes')
        ->mergeBindings($salario);
        return $retorno->get();
    }


    public function builderQuery($tipo = self::SALARIO)
    {
        $where = '';
        if (!empty($this->anoCompetenciaInicio) && !empty($this->mesCompetenciaInicio)) {
            $where = "where x.data_base >= '{$this->anoCompetenciaInicio}-{$this->mesCompetenciaInicio}-01'::date";
        }
        if (!empty($this->anoCompetenciaFim) && !empty($this->mesCompetenciaFim)) {
            $where = "where x.data_base <= '{$this->anoCompetenciaFim}-{$this->mesCompetenciaFim}-01'::date";
        }
        if (!empty($this->anoCompetenciaInicio)
            && !empty($this->mesCompetenciaInicio)
            && !empty($this->anoCompetenciaFim)
            && !empty($this->mesCompetenciaFim)) {
            $where = "where x.data_base between '{$this->anoCompetenciaInicio}-{$this->mesCompetenciaInicio}-01'::date
                and '{$this->anoCompetenciaFim}-{$this->mesCompetenciaFim}-01'::date";
        }

        $retorno = DB::table(self::SCHEMA . ".{$this->tabelas[$tipo]}")
            ->select(
                "rh02_regist",
                "rh02_mesusu",
                "rh02_anousu",
                DB::raw(
                    "SUM({$this->siglas[$tipo]}_valor) AS valor"
                )
            )
            ->join(
                self::SCHEMA .
                '.rhpessoalmov',
                function ($join) use ($tipo) {
                    $join->on('rh02_anousu', '=', $this->siglas[$tipo] . '_anousu');
                    $join->on('rh02_mesusu', '=', $this->siglas[$tipo] . '_mesusu');
                    $join->on('rh02_regist', '=', $this->siglas[$tipo] . '_regist');
                    $join->on('rh02_instit', '=', $this->siglas[$tipo] . '_instit');
                }
            )
            ->join(
                self::SCHEMA .
                '.rhrubricas',
                function ($join) use ($tipo) {
                    $join->on('rh27_instit', '=', 'rh02_instit');
                    $join->on('rh27_rubric', '=', "{$this->siglas[$tipo]}_rubric");
                    $join->where('rh27_tipo', 1);
                }
            )
            ->whereIn(
                DB::raw('(rh02_anousu, rh02_mesusu)'),
                DB::table(
                    DB::raw(
                        '
                        (select rh02_anousu,
                        rh02_mesusu
                        from (
                        select distinct
                            rh02_anousu,
                            rh02_mesusu
                            ,(rh02_anousu || \'-\' || rh02_mesusu || \'-01\')::date as data_base
                        from 
                            pessoal.rhpessoalmov
                        where 
                            rh02_regist = ' . $this->matricula .'
                            AND rh02_instit = ' . $this->codigoInstituicao . '
                        ) as x 
                        ' . $where . ' 
                        order by 1 asc, 2 asc) as y
                        '
                    )
                )
            )
            ->where('rh02_regist', '=', $this->matricula)
            ->where('rh02_instit', '=', $this->codigoInstituicao)
            ->where('rh27_pd', 1);
        
        $retorno
            ->groupBy('rh02_regist')
            ->groupBy('rh02_anousu')
            ->groupBy('rh02_mesusu')
            ->groupBy('rh27_pd');
            
        return $retorno;
    }
}
