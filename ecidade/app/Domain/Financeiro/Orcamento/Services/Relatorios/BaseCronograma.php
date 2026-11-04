<?php

namespace App\Domain\Financeiro\Orcamento\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;

abstract class BaseCronograma
{
    /**
     * @var array
     */
    protected $dados = [];

    /**
     * @var integer
     */
    protected $exercicio;

    /**
     * @var string
     */
    protected $periodicidade;

    /**
     * @var array
     */
    protected $instituicoes = [];

    /**
     * @var string
     */
    protected $agruparPor;

    protected $meses = [
        'janeiro',
        'fevereiro',
        'marco',
        'abril',
        'maio',
        'junho',
        'julho',
        'agosto',
        'setembro',
        'outubro',
        'novembro',
        'dezembro',
    ];
    protected $bimestres = [
        'bimestre_1',
        'bimestre_2',
        'bimestre_3',
        'bimestre_4',
        'bimestre_5',
        'bimestre_6',
    ];

    /**
     * @param array $filtros
     */
    public function __construct(array $filtros)
    {
        $this->processaFiltros($filtros);
    }

    abstract protected function processaFiltros(array $filtros);

    /**
     * Inicializa os totalizadores
     */
    protected function inicializaTotalizadores()
    {
        if ($this->periodicidade === 'mensal') {
            $this->dados['totalizador'] = (object)[
                'valor' => 0,
                'janeiro' => 0,
                'fevereiro' => 0,
                'marco' => 0,
                'abril' => 0,
                'maio' => 0,
                'junho' => 0,
                'julho' => 0,
                'agosto' => 0,
                'setembro' => 0,
                'outubro' => 0,
                'novembro' => 0,
                'dezembro' => 0
            ];
        } else {
            $this->dados['totalizador'] = (object)[
                'valor' => 0,
                'bimestre_1' => 0,
                'bimestre_2' => 0,
                'bimestre_3' => 0,
                'bimestre_4' => 0,
                'bimestre_5' => 0,
                'bimestre_6' => 0,
            ];
        }
    }

    /**
     * Totaliza
     */
    protected function totalizar()
    {
        foreach ($this->dados['dados'] as $dado) {
            if ($this->periodicidade === 'mensal') {
                $this->dados['totalizador']->valor += $dado->valor;
                $this->dados['totalizador']->janeiro += $dado->janeiro;
                $this->dados['totalizador']->fevereiro += $dado->fevereiro;
                $this->dados['totalizador']->marco += $dado->marco;
                $this->dados['totalizador']->abril += $dado->abril;
                $this->dados['totalizador']->maio += $dado->maio;
                $this->dados['totalizador']->junho += $dado->junho;
                $this->dados['totalizador']->julho += $dado->julho;
                $this->dados['totalizador']->agosto += $dado->agosto;
                $this->dados['totalizador']->setembro += $dado->setembro;
                $this->dados['totalizador']->outubro += $dado->outubro;
                $this->dados['totalizador']->novembro += $dado->novembro;
                $this->dados['totalizador']->dezembro += $dado->dezembro;
            } else {
                $this->dados['totalizador']->valor += $dado->valor;
                $this->dados['totalizador']->bimestre_1 += $dado->bimestre_1;
                $this->dados['totalizador']->bimestre_2 += $dado->bimestre_2;
                $this->dados['totalizador']->bimestre_3 += $dado->bimestre_3;
                $this->dados['totalizador']->bimestre_4 += $dado->bimestre_4;
                $this->dados['totalizador']->bimestre_5 += $dado->bimestre_5;
                $this->dados['totalizador']->bimestre_6 += $dado->bimestre_6;
            }
        };
    }

    /**
     * @param integer $anoSessao
     * @param integer $instituicao
     * @param integer $codigoRelatorio
     * @param integer $periodo
     */
    protected function buscarNotasExplicativas($anoSessao, $instituicao, $codigoRelatorio, $periodo = 1)
    {
        $sql = "
            select o42_nota as nota, o42_fonte as fonte
              from orcparamrelnota
             where orcparamrelnota.o42_codparrel = {$codigoRelatorio}
               and orcparamrelnota.o42_periodo = '{$periodo}'
               and orcparamrelnota.o42_anousu = {$anoSessao}
               and orcparamrelnota.o42_instit = {$instituicao};
        ";

        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            $notasExplicativa = \db_utils::fieldsMemory($rs, 0);
            $notasExplicativa->fonte = trim($notasExplicativa->fonte);

            $this->dados['fonte'] = "";
            if (!empty($notasExplicativa->fonte)) {
                $this->dados['fonte'] = "Fonte: {$notasExplicativa->fonte}";
            }
            $notasExplicativa->nota = trim($notasExplicativa->nota);
            $this->dados['notaExplicativa'] = "";
            if (!empty($notasExplicativa->nota)) {
                $this->dados['notaExplicativa'] = "Nota Explicativa: {$notasExplicativa->nota}";
            }
        }
    }

    /**
     * @return string[]
     */
    protected function getPeriodos()
    {
        $periodos = $this->bimestres;
        if ($this->periodicidade === 'mensal') {
            $periodos = $this->meses;
        }
        return $periodos;
    }
}
