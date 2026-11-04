<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\DCASP;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use App\Domain\Financeiro\Orcamento\Models\FontesSiconfi;
use ECidade\Financeiro\Contabilidade\Relatorio\DCASP\Layout\BalancoFincanceiroDcasp2024Pdf;

class BalancoFincanceiroDcasp2024 extends AnexosService
{
    /**
     * @var boolean
     */
    private $exibirExercicioAnterior = true;
    /**
     * @var string
     */
    private $tipoImpressao;

    protected $linhasProcessarRecursoReceita = [2, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14];
    protected $linhasProcessarRecursoDespesa = [35, 37, 38, 39, 40, 41, 42, 43, 45, 46, 47];

    protected $linhasNaoProcessar = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 21, 24, 29, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46,
        47, 54, 57, 58, 59, 62, 66
    ];

    protected $totalizarSoma = [
        1 => [2, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14],
        3 => [4, 5, 6, 7, 8, 9, 10],
        11 => [12, 13, 14],
        15 => [16, 17, 18, 19, 20],
        21 => [22, 23],
        24 => [25, 26, 27, 28],
        29 => [30, 31, 32],
        33 => [1, 15, 21, 24, 29],
        34 => [35, 37, 38, 39, 40, 41, 42, 43, 45, 46, 47],
        36 => [37, 38, 39, 40, 41, 42, 43],
        44 => [45, 46, 47],
        48 => [49, 50, 51, 52, 53],
        54 => [55, 56],
        57 => [58, 59, 60, 61],
        62 => [63, 64, 65],
        66 => [34, 48, 54, 57, 62],
    ];


    /**
     * @var string[] lista das classificações de recursos
     */
    protected $classificacaoRecursos = [
        2 => 'Recursos Livres (não vinculados)',
        3 => 'Recursos Vinculados à Educação',
        4 => 'Recursos Vinculados à Saude',
        5 => 'Recursos Vinculados à Assistência Social',
        6 => 'Demais Vinculações Decorrentes de Transferências',
        7 => 'Demais Vinculações Legais',
        8 => 'Recursos Vinculados à Previdência social',
        9 => 'Recursos Extraorçamentários',
        10 => 'Outras Vinculações'
    ];


    protected $linhasOrganizadas = [];

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
        $this->exercicio = $filtros['DB_anousu'];
        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($filtros['DB_instit']);
        $this->constructAssinaturas($filtros['DB_instit']);
        $this->constructInstituicoes(DBConfig::whereIn('codigo', $filtros['instituicoes'])->get());
        $this->constructPeriodo($filtros['periodo']);
        $this->constructRelatorio($filtros['codigo_relatorio']);
    }

    public function emitir()
    {
        $this->processar();

        $pdf = new BalancoFincanceiroDcasp2024Pdf();

        $mes = \DBDate::getMesExtenso($this->periodo->getMesFinal());
        $lista = $this->instituicoes->map(function (DBConfig $config) {
            return $config->nomeinst;
        })->implode(', ');


        $pdf->setTipoImpressao($this->filtros['tipoImpressao']);
        $tipo = $this->filtros['tipoImpressao'] === 'A' ? 'Analítico' : 'Sintético';
        $pdf->headers($this->exercicio, $mes, $lista, $tipo);
        $pdf->imprimeQuadroAuxiliar($this->filtros['quadroAuxiliar']);
        $pdf->setDados($this->linhasOrganizadas);
        $pdf->setNotaExplicativa($this->getNotaExplicativa());
        $pdf->emitir();
    }

    private function processar()
    {
        $this->criaProriedadesValor();
        $this->criaPropriedadesReceita();
        $this->mapeiaRecursosLinhas();
        $this->executarBalancetes();
        $this->processarLinhasRecursoReceita();
        $this->processarLinhasRecursoDespesa();
        $this->processaLinhasBalanceteVerificacao();
        $this->processaLinhasBalanceteDespesa();
        $this->processaLinhasRP();
        $this->processaValoresManuais($this->linhas);
        $this->calcularSoma();
        $this->calcularLinhaQuadroAuxiliar(3);
        $this->calcularLinhaQuadroAuxiliar(11);
        $this->organizaDados();
    }

    private function executarBalancetes()
    {
        $exercicio = $this->exercicio;

        $this->balanceteReceita[$exercicio] = $this->executarBalanceteReceita(
            $exercicio,
            "$exercicio-01-01",
            $this->periodo->getDataFinal($exercicio)->getDate()
        );
        $this->balanceteDespesa[$exercicio] = $this->executarBalanceteDespesa(
            $exercicio,
            "$exercicio-01-01",
            $this->periodo->getDataFinal($exercicio)->getDate()
        );
        $this->balanceteVerificacao[$exercicio] = $this->executarBalanceteVerificacao(
            $exercicio,
            "$exercicio-01-01",
            $this->periodo->getDataFinal($exercicio)->getDate()
        );
        foreach ($this->balanceteVerificacao[$exercicio] as $conta) {
            $this->ajustaSaldosBalanceteVerificacao($conta);
        }

        $this->getDadosRestosPagarPorExercicio($this->exercicio);

        if ($this->filtros['imprimirValorExercicioAnterior']) {
            $anterior = $exercicio - 1;
            $this->balanceteReceita[$anterior] = $this->executarBalanceteReceita(
                $anterior,
                "$anterior-01-01",
                $this->periodo->getDataFinal($anterior)->getDate()
            );
            $this->balanceteDespesa[$anterior] = $this->executarBalanceteDespesa(
                $anterior,
                "$anterior-01-01",
                $this->periodo->getDataFinal($anterior)->getDate()
            );
            $this->balanceteVerificacao[$anterior] = $this->executarBalanceteVerificacao(
                $anterior,
                "$anterior-01-01",
                $this->periodo->getDataFinal($anterior)->getDate()
            );

            foreach ($this->balanceteVerificacao[$anterior] as $conta) {
                $this->ajustaSaldosBalanceteVerificacao($conta);
            }

            $this->getDadosRestosPagarPorExercicio($anterior);
        }
    }

    private function processarLinhasRecursoReceita()
    {
        $balancete = $this->balanceteReceita[$this->exercicio];
        if ($this->filtros['imprimirValorExercicioAnterior']) {
            $anterior = $this->exercicio - 1;
            $balanceteAnterior = $this->balanceteReceita[$anterior];
        }
        foreach ($this->linhasProcessarRecursoReceita as $ordem) {
            $this->calculaLinhaRecursoReceita($this->linhas[$ordem], $balancete, 'corrente');
            if ($this->filtros['imprimirValorExercicioAnterior']) {
                $this->calculaLinhaRecursoReceita($this->linhas[$ordem], $balanceteAnterior, 'anterior');
            }
        }

        unset($this->balanceteReceita);
    }


    public function getHashRecursos($siconfi, $subrecurso, $complemento)
    {
        if ($this->filtros['apresentarRecurso'] === 'siconfi') {
            return "{$siconfi}#{$complemento}";
        }
        return "{$subrecurso}#{$complemento}";
    }

    private function calculaLinhaRecursoReceita(\stdClass $linha, $balancete, $periocidade)
    {
        $propriedadeArrecadacao = "arrecadacao_$periocidade";
        $propriedadeDeducao = "deducao_$periocidade";
        $propriedadeExercicio = "exercicio_$periocidade";

        foreach ($balancete as $receita) {
            $siconfi = substr($receita->siconfi, 1, 3);
            if (in_array($siconfi, $linha->recursos)) {
                // total geral da linha no exercício
                $linha->$propriedadeExercicio += $receita->arrecadado_acumulado;

                // separa os valores em arrecadação e dedução
                if ($receita->classe === 4) {
                    $linha->$propriedadeArrecadacao += $receita->arrecadado_acumulado;
                }
                if ($receita->classe === 9) {
                    $linha->$propriedadeDeducao += $receita->arrecadado_acumulado;
                }

                // agrupa os valores por recurso
                $complemento = $receita->complemento_lancamento;
                $hash = $this->getHashRecursos($receita->siconfi, $receita->fonte_recurso, $complemento);
                if (!array_key_exists($hash, $linha->porRecurso)) {
                    $linha->porRecurso[$hash] = (object)[
                        'descricao' => str_replace('#', ' - ', $hash),
                        'exercicio_corrente' => 0,
                        'exercicio_anterior' => 0
                    ];
                }

                $linha->porRecurso[$hash]->$propriedadeExercicio += $receita->arrecadado_acumulado;
            }
        }
    }


    /**
     * Mapeia os recursos que devem ser usado em cada linha.
     * @return void
     */
    private function mapeiaRecursosLinhas()
    {
        $this->linhas[35]->recursos = $this->linhas[2]->recursos = $this->getFontesClassificacao(2);
        $this->linhas[37]->recursos = $this->linhas[4]->recursos = $this->getFontesClassificacao(3);
        $this->linhas[38]->recursos = $this->linhas[5]->recursos = $this->getFontesClassificacao(4);
        $this->linhas[39]->recursos = $this->linhas[6]->recursos = $this->getFontesClassificacao(5);
        $this->linhas[40]->recursos = $this->linhas[7]->recursos = [803, 804];
        $this->linhas[41]->recursos = $this->linhas[8]->recursos = $this->getFontesClassificacao(6);
        $this->linhas[42]->recursos = $this->linhas[9]->recursos = $this->getFontesClassificacao(7);
        $this->linhas[43]->recursos = $this->linhas[10]->recursos = $this->getFontesClassificacao(10);
        $this->linhas[45]->recursos = $this->linhas[12]->recursos = [800];
        $this->linhas[46]->recursos = $this->linhas[13]->recursos = [801];
        $this->linhas[47]->recursos = $this->linhas[14]->recursos = [802];
    }

    private function getFontesClassificacao($classificacao)
    {
        return FontesSiconfi::select('codigo_siconfi')
            ->where('classificacaofr_id', $classificacao)
            ->get()
            ->map(function ($linha) {
                return $linha->codigo_siconfi;
            })->toArray();
    }

    private function processarLinhasRecursoDespesa()
    {
        $balancete = $this->balanceteDespesa[$this->exercicio];
        if ($this->filtros['imprimirValorExercicioAnterior']) {
            $anterior = $this->exercicio - 1;
            $balanceteAnterior = $this->balanceteDespesa[$anterior];
        }
        foreach ($this->linhasProcessarRecursoDespesa as $ordem) {
            $this->calculaLinhaRecursoDespesa($this->linhas[$ordem], $balancete, 'corrente');
            if ($this->filtros['imprimirValorExercicioAnterior']) {
                $this->calculaLinhaRecursoDespesa($this->linhas[$ordem], $balanceteAnterior, 'anterior');
            }
        }
    }

    private function calculaLinhaRecursoDespesa(\stdClass $linha, $balancete, $periocidade)
    {
        $propriedadeExercicio = "exercicio_$periocidade";

        foreach ($balancete as $despesa) {
            $siconfi = substr($despesa->siconfi, 1, 3);
            if (in_array($siconfi, $linha->recursos)) {
                $linha->$propriedadeExercicio += $despesa->empenhado_liquido;

                // agrupa os valores por recurso
                $hash = $this->getHashRecursos($despesa->siconfi, $despesa->fonte_recurso, $despesa->complemento);
                if (!array_key_exists($hash, $linha->porRecurso)) {
                    $linha->porRecurso[$hash] = (object)[
                        'descricao' => str_replace('#', ' - ', $hash),
                        'exercicio_corrente' => 0,
                        'exercicio_anterior' => 0
                    ];
                }
                $linha->porRecurso[$hash]->$propriedadeExercicio += $despesa->empenhado_liquido;
            }
        }
    }

    /**
     * Processa as linhas de RP
     */
    private function processaLinhasRP()
    {
        foreach ($this->restosPagar[$this->exercicio] as $rp) {
            $this->linhas[58]->exercicio_corrente += $rp->pagamento_rp_nao_processado;
            $this->linhas[59]->exercicio_corrente += $rp->pagamento_rp_processado;
        }

        if ($this->filtros['imprimirValorExercicioAnterior']) {
            $anterior = $this->exercicio - 1;
            foreach ($this->restosPagar[$anterior] as $rp) {
                $this->linhas[58]->exercicio_anterior += $rp->pagamento_rp_nao_processado;
                $this->linhas[59]->exercicio_anterior += $rp->pagamento_rp_processado;
            }
        }

        unset($this->restosPagar);
    }

    private function getColunaCalcularBalVer($linha, $periodicidade)
    {
        $linhasSaldoFinal = [16, 17, 18, 19, 20, 22, 23, 49, 50, 51, 52, 53, 55, 56, 63, 64, 65];
        $linhasMovCredito = [27, 28];
        $linhasMovDebito = [60, 61];
        $linhasSaldoInicial = [30, 31, 32];
        if (in_array($linha->ordem, $linhasSaldoFinal)) {
            return ['saldo_final_acumulado' => "exercicio_{$periodicidade}"];
        }

        if (in_array($linha->ordem, $linhasMovCredito)) {
            return ['saldo_credito_no_periodo' => "exercicio_{$periodicidade}"];
        }

        if (in_array($linha->ordem, $linhasMovDebito)) {
            return ['saldo_debito_no_periodo' => "exercicio_{$periodicidade}"];
        }

        if (in_array($linha->ordem, $linhasSaldoInicial)) {
            return ['saldo_anterior_no_periodo' => "exercicio_{$periodicidade}"];
        }
    }

    /**
     * @return void
     */
    private function processaLinhasBalanceteVerificacao()
    {
        $linhasBalVer = [
            16, 17, 18, 19, 20, 22, 23, 27, 28, 30, 31, 32, 49, 50, 51, 52, 53, 55, 56, 60, 61, 63, 64, 65
        ];

        foreach ($linhasBalVer as $ordem) {
            $linha = $this->linhas[$ordem];
            $this->colunasVerificacao = $this->getColunaCalcularBalVer($linha, 'corrente');
            $this->processaBalanceteVerificacao($this->getDadosVerificacaoPorExercicio($this->exercicio), $linha);

            if ($this->filtros['imprimirValorExercicioAnterior']) {
                $this->colunasVerificacao = $this->getColunaCalcularBalVer($linha, 'anterior');
                $anterior = $this->exercicio - 1;
                $this->processaBalanceteVerificacao($this->getDadosVerificacaoPorExercicio($anterior), $linha);
            }
        }
        unset($this->balanceteVerificacao);

        /**
         * Depois de calcular o balancete de verificação, atualiza a propriedade da coluna na raiz do objeto
         */
        foreach ($linhasBalVer as $ordem) {
            $linha = $this->linhas[$ordem];
            foreach ($linha->colunas as $coluna) {
                $linha->{$coluna->coluna} = $coluna->valor;
            }
        }
    }

    private function organizaDados()
    {
        $ingressos = range(1, 33);
        $dispendios = range(34, 66);
        $quadroAuxiliar = range(2, 14);

        foreach ($ingressos as $linha) {
            $this->linhasOrganizadas['ingressos'][$linha] = $this->linhas[$linha];
        }
        foreach ($dispendios as $linha) {
            $this->linhasOrganizadas['dispendios'][$linha] = $this->linhas[$linha];
        }
        foreach ($quadroAuxiliar as $linha) {
            $this->linhasOrganizadas['quadro_auxiliar'][$linha] = $this->linhas[$linha];
        }
    }

    private function criaPropriedadesReceita()
    {
        foreach ($this->linhasProcessarRecursoReceita as $ordem) {
            $this->linhas[$ordem]->arrecadacao_corrente = 0;
            $this->linhas[$ordem]->arrecadacao_anterior = 0;
            $this->linhas[$ordem]->deducao_corrente = 0;
            $this->linhas[$ordem]->deducao_anterior = 0;
            $this->linhas[$ordem]->porRecurso = [];
        }

        foreach ($this->linhasProcessarRecursoDespesa as $ordem) {
            $this->linhas[$ordem]->porRecurso = [];
        }

        // linha totalizadora "Recursos Vinculados (EXCETO AO RPPS)" usada no quadro auxiliar
        $this->linhas[3]->arrecadacao_corrente = 0;
        $this->linhas[3]->arrecadacao_anterior = 0;
        $this->linhas[3]->deducao_corrente = 0;
        $this->linhas[3]->deducao_anterior = 0;

        // linha totalizadora "Recursos Vinculados (EXCETO AO RPPS)" usada no quadro auxiliar
        $this->linhas[11]->arrecadacao_corrente = 0;
        $this->linhas[11]->arrecadacao_anterior = 0;
        $this->linhas[11]->deducao_corrente = 0;
        $this->linhas[11]->deducao_anterior = 0;
    }

    private function calcularLinhaQuadroAuxiliar($linha)
    {
        $somar = $this->totalizarSoma[$linha];
        foreach ($somar as $idLinhaSoma) {
            $this->linhas[$linha]->arrecadacao_corrente += $this->linhas[$idLinhaSoma]->arrecadacao_corrente;
            $this->linhas[$linha]->arrecadacao_anterior += $this->linhas[$idLinhaSoma]->arrecadacao_anterior;
            $this->linhas[$linha]->deducao_corrente += $this->linhas[$idLinhaSoma]->deducao_corrente;
            $this->linhas[$linha]->deducao_anterior += $this->linhas[$idLinhaSoma]->deducao_anterior;
        }
    }

    private function processaLinhasBalanceteDespesa()
    {
        $linhas = [25, 26];
        foreach ($linhas as $ordem) {
            $linha = $this->linhas[$ordem];
            $this->colunasDespesa = $this->getColunaCalcularBalDesp($linha, 'corrente');
            $this->processaDespesa($this->getDadosDespesaPorExercicio($this->exercicio), $linha);

            if ($this->filtros['imprimirValorExercicioAnterior']) {
                $this->colunasDespesa = $this->getColunaCalcularBalDesp($linha, 'anterior');
                $exercicio = $this->exercicio - 1;
                $this->processaDespesa($this->getDadosDespesaPorExercicio($exercicio), $linha);
            }
        }

        foreach ($linhas as $ordem) {
            $linha = $this->linhas[$ordem];
            foreach ($linha->colunas as $coluna) {
                $linha->{$coluna->coluna} = $coluna->valor;
            }
        }
    }

    private function getColunaCalcularBalDesp(\stdClass $linha, $periodicidade)
    {
        if ($linha->ordem == 25) {
            return ['a_liquidar' => "exercicio_{$periodicidade}"];
        }

        if ($linha->ordem == 26) {
            return ['a_pagar_liquidado' => "exercicio_{$periodicidade}"];
        }

        return ['empenhado_liquido' => "exercicio_{$periodicidade}"];
    }

    /**
     * Calcula as xx exercicio_atual e exercicio_anterior, a partir dos valores contidos nas colunas.
     * @param array $linhas
     * @return void
     */
    protected function processaValoresManuais(&$linhas)
    {
        foreach ($linhas as $linha) {
            foreach ($linha->colunas as $coluna) {
                $linha->{$coluna->coluna} += $coluna->valorManual;
            }
        }
    }
}
