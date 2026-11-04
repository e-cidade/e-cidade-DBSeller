<?php
namespace App\Domain\RecursosHumanos\Pessoal\Repository;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\RecursosHumanos\Pessoal\Model\Instituicao\Instituicao;
use App\Domain\RecursosHumanos\Pessoal\Model\RhLota;
use App\Domain\RecursosHumanos\Pessoal\Model\Calculo\Basesr;
use App\Domain\RecursosHumanos\Pessoal\Model\Calculo\Gerfcom;
use App\Domain\RecursosHumanos\Pessoal\Model\Calculo\Gerfres;
use App\Domain\RecursosHumanos\Pessoal\Model\Calculo\Gerfsal;
use App\Domain\RecursosHumanos\Pessoal\Model\InssIrf\InssIrf;
use App\Domain\RecursosHumanos\Pessoal\Model\Servidor\RhPessoalMov;
use App\Domain\RecursosHumanos\Pessoal\Model\Selecao;
use funcao;
use Illuminate\Support\Facades\DB;

class TipoGuiaPrevidenciaRepository extends BaseRepository
{
    const SALARIO = 'salario';
    const COMPLEMENTAR = 'complementar';
    const RESCISAO = 'rescisao';
    const DECIMO = 'decimo';

    /**
     * @var int
     */
    private $ano;
    
    /**
     * @var int
     */
    private $mes;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var []
     */
    private $lotacoes;

    /**
     * @var int
     */
    private $selecao;

    /**
     * @var string
     */
    private $arquivo;

    /**
     * @var int
     */
    private $codigoInstituicao;


    /**
     * @var string
     */
    private $nomeInstituicao;

    /**
     * @var string
     */
    private $dataVencimento;


    /**
     * @var string
     */
    private $tipoGuia;

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
     * @var array
     */
    private $siglas = [
        self::SALARIO => "r14",
        self::COMPLEMENTAR => "r48",
        self::RESCISAO => "r20",
        self::DECIMO => "r35",
    ];

    /**
     * @var string
     */
    private $whereSelecao = "";

    /**
     * @var array
     */
    private $bases = ['B995'];

    const SCHEMA = 'pessoal';

    /**
     * @var $rubricas = []
     */
    private $rubricas = [
        "R901",
        "R902",
        "R903",
        "R904",
        "R905",
        "R906",
        "R907",
        "R908",
        "R909",
        "R910",
        "R911",
        "R912"
    ];

    /**
     * @var string
     */
    private $rubricasInline = "";

    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setLotacoes($lotacoes)
    {
        $this->lotacoes = $lotacoes;
    }

    public function setSelecao($selecao)
    {
        $this->selecao = $selecao;
    }

    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    public function setNomeInstituicao($nomeInstituicao)
    {
        $this->nomeInstituicao = $nomeInstituicao;
    }

    public function setDataVencimento($dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;
    }

    public function setTipoGuia($tipoGuia)
    {
        $this->tipoGuia = $tipoGuia;
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
        $this->setup();
        $salario = null;
        if ($this->arquivo == self::SALARIO) {
            $salario = $this->builderQuery(self::SALARIO);
            $complementar = $this->builderQuery(self::COMPLEMENTAR);
            $rescisao = $this->builderQuery(self::RESCISAO);
            $salario->unionAll($complementar);
            $salario->unionAll($rescisao);
        } else {
            $salario = $this->builderQuery(self::DECIMO);
        }
        $retorno = DB::table(
            DB::raw(
                "(
                {$salario->toSql()}
            ) as x"
            )
        )
            ->select(
                DB::raw("count(r01_regist) AS soma"),
                DB::raw("sum(base) AS base"),
                DB::raw("sum(ded) AS ded"),
                DB::raw("sum(dev) AS dev"),
                DB::raw("sum(desco) AS desco"),
                DB::raw("(sum(base)/100) * 16 AS parcela_patronal")
            )
            ->mergeBindings($salario);
        
        if (sizeof($this->lotacoes) > 0) {
            $retorno->addSelect("rh02_lota AS lotacao");
            $retorno->groupBy('rh02_lota');
        }
        if ($retorno->count()==0) {
            return $retorno->count();
        } else {
            return $retorno->get();
        }
    }


    public function builderQuery($tipo = self::SALARIO)
    {
        $retorno = DB::table(self::SCHEMA . ".{$this->tabelas[$tipo]}")
            ->select(
                "rh02_regist AS r01_regist",
                "rh02_lota",
                DB::raw(
                    "
                    SUM(
                        CASE WHEN {$this->siglas[$tipo]}_rubric IN ({$this->rubricasInline})
                            THEN {$this->siglas[$tipo]}_valor 
                            ELSE 0 
                    END) AS desco"
                ),
                DB::raw(
                    "
                    SUM(
                        CASE WHEN r09_rubric is not null THEN {$this->siglas[$tipo]}_valor ELSE 0 END
                    ) AS ded"
                ),
                DB::raw(
                    "SUM(CASE WHEN {$this->siglas[$tipo]}_rubric IN ('') THEN
                    {$this->siglas[$tipo]}_valor ELSE 0 END) AS dev"
                ),
                DB::raw(
                    "SUM(CASE WHEN {$this->siglas[$tipo]}_rubric IN ('R992') THEN
                    {$this->siglas[$tipo]}_valor ELSE 0 END) AS base"
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
                '.rhlota',
                function ($join) {
                    $join->on('r70_instit', '=', 'rh02_instit');
                    $join->on('r70_codigo', '=', 'rh02_lota');
                }
            )
            ->leftJoin(
                self::SCHEMA .
                '.basesr',
                function ($join) use ($tipo) {
                    $join->on('r09_anousu', '=', 'rh02_anousu');
                    $join->on('r09_mesusu', '=', 'rh02_mesusu');
                    $join->on('r09_instit', '=', 'rh02_instit');
                    $join->on('r09_rubric', '=', $this->siglas[$tipo] . '_rubric');
                    $join->whereIn('r09_base', $this->bases);
                }
            )
            ->where('rh02_anousu', '=', $this->ano)
            ->where('rh02_mesusu', '=', $this->mes)
            ->where('rh02_instit', '=', $this->codigoInstituicao)
            ->whereIn('rh02_tbprev', $this->tabelaPrevidencia)
            ->where(
                function ($aux) use ($tipo) {
                    $aux->whereIn($this->siglas[$tipo] . '_rubric', $this->rubricas);
                    $aux->orWhereNotNull('r09_rubric');
                    $aux->orWhereIn($this->siglas[$tipo] . '_rubric', ['R992']);
                }
            );

        if (!empty($this->whereSelecao)) {
            $retorno->whereRaw($this->whereSelecao);
        }
        
        if (sizeof($this->lotacoes) > 0) {
            $retorno->whereIn('rh02_lota', $this->lotacoes);
        }
        
        $retorno->groupBy('rh02_regist')
            ->groupBy('rh02_lota');
        return $retorno;
    }

    private function setup()
    {
        if (!empty($this->selecao)) {
            $this->whereSelecao = Selecao::select('r44_where')
                ->where('r44_selec', '=', $this->selecao)
                ->where('r44_instit', '=', $this->codigoInstituicao)
                ->orderBy('r44_selec')
                ->first()->getWhere();
        }

        $this->rubricasInline = "";
        foreach ($this->rubricas as $rubrica) {
            $this->rubricasInline .= "'{$rubrica}', ";
        }
        if (!empty($this->rubricasInline)) {
            $this->rubricasInline = substr($this->rubricasInline, 0, -2);
        }
    }

    public function getInstituicao()
    {
        return Instituicao::find($this->codigoInstituicao);
    }

    public function getNomeLota($codigo)
    {
        return RhLota::select('r70_descr', 'r70_codigo')
            ->where('r70_codigo', '=', $codigo)
            ->get();
    }

    public function getCountRhLota($codigo, $ano, $mes, $tabelas)
    {
        $tabelas = $this->convertPrevidencia($tabelas);
        return RhPessoalMov::where('rh02_lota', $codigo)
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes)
            ->where('rh02_instit', $this->codigoInstituicao)
            ->where('rh02_tbprev', $tabelas)
            ->join(self::SCHEMA.'.rhlota', 'rh02_lota', '=', 'r70_codigo')
            ->where('r70_ativo', '=', true)
            ->join(self::SCHEMA.'.gerfsal', 'rh02_regist', '=', 'r14_regist')
            ->where('r14_anousu', '=', $ano)
            ->where('r14_mesusu', '=', $mes)
            ->where(function ($query) {
                $query->whereIn('r14_rubric', $this->rubricas);
            })
        ->count();
    }

    public function dadosPatronal($ano, $mes, $tabelas)
    {
        $inssIrf = InssIrf::where('r33_anousu', $ano)
            ->where('r33_mesusu', $mes)
            ->where('r33_instit', $this->codigoInstituicao)
            ->where('r33_codtab', $tabelas)
            ->whereNotNull('r33_ppatro')
            ->get(['r33_ppatro', 'r33_codtab', 'r33_nome']);
            
            $dadosFormatados = [];
        foreach ($inssIrf as $inssirf) {
            $dadosFormatados[] = [
                'nome' => trim($inssirf->r33_nome),
                'porcent' => $inssirf->r33_ppatro,
                'tabela' => $inssirf->r33_codtab
            ];
        }
           return $dadosFormatados;
    }

    private function convertPrevidencia($tabelas)
    {
        //3 INSS = 1
        //4 PREVIDENCIA MUN = 2
        //5 INSS AUTONOMOS = 3
        if (is_array($tabelas)) {
            foreach ($tabelas as $chave => $tabela) {
                if ($tabela === '3') {
                    $tabelas[$chave] = 1;
                }
                if ($tabela === '4') {
                    $tabelas[$chave] = 2;
                }
                if ($tabela === '5') {
                    $tabelas[$chave] = 3;
                }
            }
        } else {
            if ($tabelas === '3') {
                $tabelas = 1;
            }
            if ($tabelas === '4') {
                $tabelas = 2;
            }
            if ($tabelas === '5') {
                $tabelas = 3;
            }
        }
        
        return $tabelas;
    }
}
