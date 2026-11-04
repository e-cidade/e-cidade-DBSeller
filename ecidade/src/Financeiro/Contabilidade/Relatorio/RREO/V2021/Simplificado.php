<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2021;

use AnexoRREOFactory;
use AnexoXVIIIResumido;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoDozeFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoOitoFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoQuatroFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoSeisFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoUmFactory;
use ECidade\Financeiro\Contabilidade\Calculo\ReceitaCorrenteLiquida;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoI as FactoryAnexoI;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoIII as AnexoIIIFactory;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoIV as FactoryAnexoIV;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoVI as FactoryRelatorio;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoVIII as FactoryAnexoVIII;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoXII as FactoryAnexoXII;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoXIII as FactoryAnexoXIII;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2017\AnexoVII;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2017\LinhaAnexoVII;
use Exception;
use InstituicaoRepository;
use ParameterException;
use Periodo;
use stdClass;

/**
 * Class Simplificado
 * @package ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2018
 */
class Simplificado extends AnexoXVIIIResumido
{
    /**
     * @var int
     */
    const CODIGO_RELATORIO = 181;

    /**
     * @var
     */
    private $dadosAnexoI;

    /**
     * Simplificado constructor.
     * @param $iAnoUsu
     * @param $iCodigoRelatorio
     * @param $iCodigoPeriodo
     */
    public function __construct($iAnoUsu, $iCodigoRelatorio, $iCodigoPeriodo)
    {
        parent::__construct($iAnoUsu, self::CODIGO_RELATORIO, $iCodigoPeriodo);

        $this->emitir();
    }

    /**
     * Emite os dados do balancete Orçamentário
     */
    public function getBalancoOrcamentario()
    {
        $dadosBalanco = $this->calcularAnexoI();
        /**
         * receita
         */
        $this->aLinhasConsistencia[2]->ate_bimestre = $dadosBalanco->nPrevisaoInicial;
        $this->aLinhasConsistencia[3]->ate_bimestre = $dadosBalanco->nPrevisaoAtualizada;
        $this->aLinhasConsistencia[4]->ate_bimestre = $dadosBalanco->nReceitasRealizadas;
        $this->aLinhasConsistencia[5]->ate_bimestre = $dadosBalanco->nDeficitOrcamentario;
        $this->aLinhasConsistencia[6]->ate_bimestre = $dadosBalanco->nSaldoExerciciosAnteriores;
        /**
         * Despesa
         */
        $this->aLinhasConsistencia[8]->ate_bimestre = $dadosBalanco->nDotacaoInicial;
        $this->aLinhasConsistencia[9]->ate_bimestre = $dadosBalanco->nCreditoAdicional;
        $this->aLinhasConsistencia[10]->ate_bimestre = $dadosBalanco->nDotacaoAtualizada;
        $this->aLinhasConsistencia[11]->ate_bimestre = $dadosBalanco->nEmpenhadas;
        $this->aLinhasConsistencia[12]->ate_bimestre = $dadosBalanco->nLiquidadas;
        $this->aLinhasConsistencia[13]->ate_bimestre = $dadosBalanco->nPagas;
        $this->aLinhasConsistencia[14]->ate_bimestre = $dadosBalanco->nSuperavitOrcamentario;
        $linhasBalanco = array_slice($this->aLinhasConsistencia, 0, 14, true);
        return $linhasBalanco;
    }

    public function getBalancoOrcamentarioNovo()
    {
        $filtros = [
            'codigo_relatorio' => AnexoUmFactory::getCodigoRelatorio($this->iAnoUsu),
            'periodo' => $this->oPeriodo->getCodigo(),
            'instituicoes' => explode(',', $this->getInstituicoes()),
            'DB_anousu' => $this->iAnoUsu,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoUmFactory::getService($this->iAnoUsu, $filtros);
        return $service->getSimplificado();
    }

    /**
     * Emite o demonstrativo de Funcao/Sub-função
     * @return array
     */
    public function getDemostrativoDespesaPorFuncaoSubfuncao()
    {
        $dadosBalanco = $this->calcularAnexoI();
        $this->aLinhasConsistencia[15]->ate_bimestre = $dadosBalanco->nEmpenhadasSemSuperavit;
        $this->aLinhasConsistencia[16]->ate_bimestre = $dadosBalanco->nLiquidadas;

        return array_slice($this->aLinhasConsistencia, 14, 2);
    }

    /**
     * Emite os valores da Receita Corrente Líquida
     * @return array
     */
    public function getReceitaCorrenteLiquida()
    {
        /**
         * Versao Antiga da RCL
         */
        if ($this->iAnoUsu < 2020) {
            $receitaCorrenteLiquidaCorrente = new ReceitaCorrenteLiquida($this->iAnoUsu, null, 178);
            $valorRcl = $receitaCorrenteLiquidaCorrente->somaRCLPeriodo($this->iCodigoPeriodo);
            $this->aLinhasConsistencia[17]->ate_bimestre = $valorRcl;

            return array($this->aLinhasConsistencia[17]);
        }
        $instituicoes = InstituicaoRepository::getInstituicoes();
        $codigoInstituicoes = implode(',', array_keys($instituicoes));

        $dadosAnexoXII = AnexoIIIFactory::getInstance($this->iAnoUsu, $this->iCodigoPeriodo);
        $dadosAnexoXII->setInstituicoes($codigoInstituicoes);

        $dadosSimplificado = $dadosAnexoXII->getDadosSimplificado();
        $this->aLinhasConsistencia[17]->ate_bimestre = $dadosSimplificado->valor_rcl_mdf;
        $dadosEndividamento = new \stdClass();
        $dadosEndividamento->descricao = "Receita Corrente Líquida Ajustada para Cálculo dos Limites de Endividamento";
        $dadosEndividamento->ate_bimestre = $dadosSimplificado->valor_rcl_endividamento;
        $dadosEndividamento->totalizar = false;
        $dadosEndividamento->nivel = 1;

        $dadosPessoal = new \stdClass();
        $dadosPessoal->descricao = "Receita Corrente Líquida Ajustada para Cálculo dos Limites da Despesa ";
        $dadosPessoal->descricao .= "com Pessoal";
        $dadosPessoal->totalizar = false;
        $dadosPessoal->nivel = 1;
        $dadosPessoal->ate_bimestre = $dadosSimplificado->valor_rcl_pessoal;

        return [$this->aLinhasConsistencia[17], $dadosEndividamento, $dadosPessoal];
    }

    /**
     * Emite os dados do relatorio
     * @return array
     */
    public function emitir()
    {
        $this->aLinhasConsistencia = $this->getLinhasRelatorio();

        return $this->aLinhasConsistencia;
    }

    /**
     * Calcula os valores do Anexo I
     * @return stdClass
     */
    protected function calcularAnexoI()
    {
        if (empty($this->dadosAnexoI)) {
            $oBalancoOrcamentario = FactoryAnexoI::getInstance($this->iAnoUsu, $this->oPeriodo);
            $oBalancoOrcamentario->setDataInicial($oBalancoOrcamentario->getDataInicialPeriodo());
            $oBalancoOrcamentario->setInstituicoes($this->getInstituicoes());
            $this->dadosAnexoI = $oBalancoOrcamentario->getDadosSimplificado();
        }

        return $this->dadosAnexoI;
    }

    /**
     * Retorna os dados do demonstrativo RPPS
     * @return array
     * @throws ParameterException
     */
    public function getRegimeDePrevidencia()
    {
        $oAnexo = FactoryAnexoIV::getInstance($this->iAnoUsu, $this->oPeriodo->getCodigo());
        $dadosAnexo = $oAnexo->getDadosSimplificado();

        $this->aLinhasConsistencia[18]->descricao = "Fundo em Capitalização (PLANO PREVIDENCIÁRIO)";
        $this->aLinhasConsistencia[22]->descricao = "Fundo em Repartição (PLANO FINANCEIRO) ";

        $sDescricao = "Despesas Previdenciárias Empenhadas";

        $oLinhaDespEmpPrev = clone $this->aLinhasConsistencia[19];
        $oLinhaDespEmpPrev->descricao = $sDescricao;
        $oLinhaDespEmpPrev->ate_bimestre = $dadosAnexo->TOTAL_DESPESAS_FUNDO_CAPITALIZACAO_emp_atebim;

        $oLinhaDespEmpFinanc = clone $this->aLinhasConsistencia[19];
        $oLinhaDespEmpFinanc->descricao = $sDescricao;
        $oLinhaDespEmpFinanc->ate_bimestre = $dadosAnexo->TOTAL_DESPESAS_FUNDO_REPARTICAO_emp_atebim;

        $this->aLinhasConsistencia[19]->ate_bimestre = $dadosAnexo->TOTAL_RECEITAS_FUNDO_CAPITALIZACAO_rec_atebim;
        $this->aLinhasConsistencia[20]->ate_bimestre = $dadosAnexo->TOTAL_DESPESAS_FUNDO_CAPITALIZACAO_liq_atebim;
        $this->aLinhasConsistencia[21]->ate_bimestre =
            $dadosAnexo->RESULTADO_PREVIDENCIARIO_FUNDO_CAPITALIZACAO_desppag;

        $this->aLinhasConsistencia[23]->ate_bimestre = $dadosAnexo->TOTAL_RECEITAS_FUNDO_REPARTICAO_rec_atebim;
        $this->aLinhasConsistencia[24]->ate_bimestre = $dadosAnexo->TOTAL_DESPESAS_FUNDO_REPARTICAO_desppag;
        $this->aLinhasConsistencia[25]->ate_bimestre = $dadosAnexo->RESULTADO_PREVIDENCIARIO_FUNDO_REPARTICAO_desppag;

        $aLinhas = array();
        $aLinhas[0] = $this->aLinhasConsistencia[18]; //Regime Próprio de Previdência dos Servidores - PLANO PREVIDE
        $aLinhas[1] = $this->aLinhasConsistencia[19];
        $aLinhas[2] = $oLinhaDespEmpPrev;
        $aLinhas[3] = $this->aLinhasConsistencia[20];
        $aLinhas[4] = $this->aLinhasConsistencia[21];

        $aLinhas[5] = $this->aLinhasConsistencia[22]; // Regime Próprio de Previdência dos Servidores - PLANO FINANCE
        $aLinhas[6] = $this->aLinhasConsistencia[23];
        $aLinhas[7] = $oLinhaDespEmpFinanc;
        $aLinhas[8] = $this->aLinhasConsistencia[24];
        $aLinhas[9] = $this->aLinhasConsistencia[25];

        return $aLinhas;
    }

    public function getRegimeDePrevidenciaNovo()
    {
        $filtros = [
            'codigo_relatorio' => AnexoQuatroFactory::getCodigoRelatorio($this->iAnoUsu),
            'periodo' => $this->oPeriodo->getCodigo(),
            'DB_anousu' => $this->iAnoUsu,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoQuatroFactory::getService($this->iAnoUsu, $filtros);
        return $service->processaLinhasSimplificado();
    }

    /**
     * @return array
     * @throws Exception
     * @throws ParameterException
     */
    public function getResultadoNominalPrimario()
    {
        $dadosAnexoVI = FactoryRelatorio::getInstance($this->getAno(), $this->getPeriodo()->getCodigo());
        $dadosAnexoVI->setInstituicoes($this->getInstituicoes());
        $dadosSimplificado = $dadosAnexoVI->getDadosSimplificado();

        $this->aLinhasConsistencia[26]->descricao .= " - Acima da Linha";
        $this->aLinhasConsistencia[26]->meta_fixada_anexo_metas_fiscais = $dadosSimplificado->metaResultadoNominal;
        $this->aLinhasConsistencia[26]->resultado_apurado_ate_bimestre = $dadosSimplificado->resultadoNominal;
        $this->aLinhasConsistencia[26]->relacao_meta = $dadosSimplificado->percentualMetaNominal;

        $this->aLinhasConsistencia[27]->descricao .= " - Acima da Linha";
        $this->aLinhasConsistencia[27]->meta_fixada_anexo_metas_fiscais = $dadosSimplificado->metaResultadoPrimario;
        $this->aLinhasConsistencia[27]->resultado_apurado_ate_bimestre = $dadosSimplificado->resultadoPrimario;
        $this->aLinhasConsistencia[27]->relacao_meta = $dadosSimplificado->percentualMetaPrimario;

        return array(
            $this->aLinhasConsistencia[27],
            $this->aLinhasConsistencia[26],
        );
    }

    public function getResultadoNominalPrimarioNovo()
    {
        $filtros = [
            'codigo_relatorio' => AnexoSeisFactory::getCodigoRelatorio($this->iAnoUsu),
            'periodo' => $this->oPeriodo->getCodigo(),
            'instituicoes' => explode(',', $this->getInstituicoes()),
            'DB_anousu' => $this->iAnoUsu,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoSeisFactory::getService($this->iAnoUsu, $filtros);
        return $service->getSimplificado();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getRestosAPagar()
    {
        $oRelatorio = new AnexoVII();
        $oRelatorio->setAno($this->getAno());
        $oRelatorio->setPeriodo($this->getPeriodo());
        $aInstitucoes = $this->getInstituicoes(true);
        foreach ($aInstitucoes as $instituicao) {
            $oRelatorio->adicionarInstituicao($instituicao);
        }

        $dadosSimplificado = $oRelatorio->getDadosSimplificado();

        /**
         * Poder Executivo
         */
        if (!empty($dadosSimplificado["linhas"][LinhaAnexoVII::PODER_EXECUTIVO])) {
            $linhaPoderExecutivo = $dadosSimplificado["linhas"][LinhaAnexoVII::PODER_EXECUTIVO];
            $this->aLinhasConsistencia[29]->inscricao = $linhaPoderExecutivo->nProcessadoInscrito;
            $this->aLinhasConsistencia[35]->inscricao = $linhaPoderExecutivo->nNaoProcessadoInscrito;
            $this->aLinhasConsistencia[29]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoCancelado;
            $this->aLinhasConsistencia[35]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoCancelado;
            $this->aLinhasConsistencia[29]->pagamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoPago;
            $this->aLinhasConsistencia[35]->pagamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoPago;
            $this->aLinhasConsistencia[29]->saldo_pagar = $linhaPoderExecutivo->nProcessadoPagar;
            $this->aLinhasConsistencia[35]->saldo_pagar = $linhaPoderExecutivo->nNaoProcessadoPagar;
        }
        /**
         * Poder Legislativo
         */
        if (!empty($dadosSimplificado["linhas"][LinhaAnexoVII::PODER_LEGISLATIVO])) {
            $linhaPoderExecutivo = $dadosSimplificado["linhas"][LinhaAnexoVII::PODER_LEGISLATIVO];
            $this->aLinhasConsistencia[30]->inscricao = $linhaPoderExecutivo->nProcessadoInscrito;
            $this->aLinhasConsistencia[36]->inscricao = $linhaPoderExecutivo->nNaoProcessadoInscrito;
            $this->aLinhasConsistencia[30]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoCancelado;
            $this->aLinhasConsistencia[36]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoCancelado;
            $this->aLinhasConsistencia[30]->pagamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoPago;
            $this->aLinhasConsistencia[36]->pagamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoPago;
            $this->aLinhasConsistencia[30]->saldo_pagar = $linhaPoderExecutivo->nProcessadoPagar;
            $this->aLinhasConsistencia[36]->saldo_pagar = $linhaPoderExecutivo->nNaoProcessadoPagar;
        }
        /**
         * Poder Judiciario
         */
        if (!empty($dadosSimplificado["linhas"][LinhaAnexoVII::PODER_JUDICIARIO])) {
            $linhaPoderExecutivo = $dadosSimplificado["linhas"][LinhaAnexoVII::PODER_JUDICIARIO];
            $this->aLinhasConsistencia[31]->inscricao = $linhaPoderExecutivo->nProcessadoInscrito;
            $this->aLinhasConsistencia[37]->inscricao = $linhaPoderExecutivo->nNaoProcessadoInscrito;
            $this->aLinhasConsistencia[31]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoCancelado;
            $this->aLinhasConsistencia[37]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoCancelado;
            $this->aLinhasConsistencia[31]->pagamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoPago;
            $this->aLinhasConsistencia[37]->pagamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoPago;
            $this->aLinhasConsistencia[31]->saldo_pagar = $linhaPoderExecutivo->nProcessadoPagar;
            $this->aLinhasConsistencia[37]->saldo_pagar = $linhaPoderExecutivo->nNaoProcessadoPagar;
        }
        /**
         * Ministério publico
         */
        if (!empty($dadosSimplificado["linhas"][LinhaAnexoVII::MINISTERIO_PUBLICO])) {
            $linhaPoderExecutivo = $dadosSimplificado["linhas"][LinhaAnexoVII::MINISTERIO_PUBLICO];
            $this->aLinhasConsistencia[32]->inscricao = $linhaPoderExecutivo->nProcessadoInscrito;
            $this->aLinhasConsistencia[38]->inscricao = $linhaPoderExecutivo->nNaoProcessadoInscrito;
            $this->aLinhasConsistencia[32]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoCancelado;
            $this->aLinhasConsistencia[38]->cancelamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoCancelado;
            $this->aLinhasConsistencia[32]->pagamento_ate_bimestre = $linhaPoderExecutivo->nProcessadoPago;
            $this->aLinhasConsistencia[38]->pagamento_ate_bimestre = $linhaPoderExecutivo->nNaoProcessadoPago;
            $this->aLinhasConsistencia[32]->saldo_pagar = $linhaPoderExecutivo->nProcessadoPagar;
            $this->aLinhasConsistencia[38]->saldo_pagar = $linhaPoderExecutivo->nNaoProcessadoPagar;
        }

        /**
         * Somamos o total dos restos a pagar e total geral
         */
        foreach (range(29, 33) as $i) {
            $cancelamentoAteBim = $this->aLinhasConsistencia[$i]->cancelamento_ate_bimestre;
            $pagoAteBim = $this->aLinhasConsistencia[$i]->pagamento_ate_bimestre;
            $this->aLinhasConsistencia[28]->inscricao += $this->aLinhasConsistencia[$i]->inscricao;
            $this->aLinhasConsistencia[28]->cancelamento_ate_bimestre += $cancelamentoAteBim;
            $this->aLinhasConsistencia[28]->pagamento_ate_bimestre += $pagoAteBim;
            $this->aLinhasConsistencia[28]->saldo_pagar += $this->aLinhasConsistencia[$i]->saldo_pagar;

            $cancelamentoAteBim = $this->aLinhasConsistencia[$i]->cancelamento_ate_bimestre;
            $pagoAteBim = $this->aLinhasConsistencia[$i]->pagamento_ate_bimestre;
            $this->aLinhasConsistencia[40]->inscricao += $this->aLinhasConsistencia[$i]->inscricao;
            $this->aLinhasConsistencia[40]->cancelamento_ate_bimestre += $cancelamentoAteBim;
            $this->aLinhasConsistencia[40]->pagamento_ate_bimestre += $pagoAteBim;
            $this->aLinhasConsistencia[40]->saldo_pagar += $this->aLinhasConsistencia[$i]->saldo_pagar;
        }
        /**
         * Somamos o total dos restos a pagar não processados e total geral
         */
        foreach (range(35, 39) as $i) {
            $cancelamentoAteBim = $this->aLinhasConsistencia[$i]->cancelamento_ate_bimestre;
            $pagoAteBim = $this->aLinhasConsistencia[$i]->pagamento_ate_bimestre;
            $this->aLinhasConsistencia[34]->inscricao += $this->aLinhasConsistencia[$i]->inscricao;
            $this->aLinhasConsistencia[34]->cancelamento_ate_bimestre += $cancelamentoAteBim;
            $this->aLinhasConsistencia[34]->pagamento_ate_bimestre += $pagoAteBim;
            $this->aLinhasConsistencia[34]->saldo_pagar += $this->aLinhasConsistencia[$i]->saldo_pagar;

            $this->aLinhasConsistencia[40]->inscricao += $this->aLinhasConsistencia[$i]->inscricao;
            $this->aLinhasConsistencia[40]->cancelamento_ate_bimestre += $cancelamentoAteBim;
            $this->aLinhasConsistencia[40]->pagamento_ate_bimestre += $pagoAteBim;
            $this->aLinhasConsistencia[40]->saldo_pagar += $this->aLinhasConsistencia[$i]->saldo_pagar;
        }

        return array(
            $this->aLinhasConsistencia[28],
            $this->aLinhasConsistencia[29],
            $this->aLinhasConsistencia[30],
            $this->aLinhasConsistencia[31],
            $this->aLinhasConsistencia[32],
            $this->aLinhasConsistencia[33],
            $this->aLinhasConsistencia[34],
            $this->aLinhasConsistencia[35],
            $this->aLinhasConsistencia[36],
            $this->aLinhasConsistencia[37],
            $this->aLinhasConsistencia[38],
            $this->aLinhasConsistencia[39],
            $this->aLinhasConsistencia[40]
        );
    }

    /**
     * @return array
     */
    public function getDespesasComManutencaoDesenvolvimentoEnsino()
    {

        $oAnexoMDE = FactoryAnexoVIII::getInstance($this->iAnoUsu, $this->oPeriodo->getCodigo());
        $oInstituicao = \InstituicaoRepository::getInstituicaoPrefeitura();

        $oAnexoMDE->setInstituicoes($this->getInstituicoes());
        $dadosSimplificado = $oAnexoMDE->getDadosSimplificado();

        $this->aLinhasConsistencia[41]->valor_apurado_ate_bimestre = $dadosSimplificado->MINIMO_ANUAL_25_ate_bim;
        $this->aLinhasConsistencia[41]->minimo_aplicar_exercicio = 25;
        $this->aLinhasConsistencia[41]->aplicado_ate_bimestre = $dadosSimplificado->MINIMO_ANUAL_25_percent_ate_bin;

        $sDescricao = "Mínimo Anual de 70% do FUNDEB na Remuneração dos Profissionais da Educação Básica";
        $this->aLinhasConsistencia[42]->descricao = $sDescricao;
        $this->aLinhasConsistencia[42]->valor_apurado_ate_bimestre = $dadosSimplificado->MINIMO_ANUAL_70_ate_bim;
        $this->aLinhasConsistencia[42]->minimo_aplicar_exercicio = 70;
        $this->aLinhasConsistencia[42]->aplicado_ate_bimestre = $dadosSimplificado->MINIMO_ANUAL_70_percent_ate_bin;

        //linhas adicionais do anexo 2021
        $sDescricao = "Percentual de 50% da Complementação da União ao FUNDEB (VAAT) na Educação Infantil";
        $oLinhaPerc50 = clone $this->aLinhasConsistencia[41];
        $oLinhaPerc50->descricao = $sDescricao;
        $oLinhaPerc50->valor_apurado_ate_bimestre = $dadosSimplificado->PERC_50_ate_bim;
        $oLinhaPerc50->minimo_aplicar_exercicio = 50;
        $oLinhaPerc50->aplicado_ate_bimestre = $dadosSimplificado->PERC_50_percent_ate_bin;

        $sDescricao = "Mínimo de 15% da Complementação da União ao FUNDEB (VAAT) em Despesas de Capital";
        $oLinhaPerc15 = clone $this->aLinhasConsistencia[42];
        $oLinhaPerc15->descricao = $sDescricao;
        $oLinhaPerc15->valor_apurado_ate_bimestre = $dadosSimplificado->MIN_15_ate_bim;
        $oLinhaPerc15->minimo_aplicar_exercicio = 15;
        $oLinhaPerc15->aplicado_ate_bimestre = $dadosSimplificado->MIN_15_percent_ate_bin;

        $aLinhas = array(

            $this->aLinhasConsistencia[41],
            $this->aLinhasConsistencia[42],
            $oLinhaPerc50,
            $oLinhaPerc15
        );

        return $aLinhas;
    }

    /**
     * Busca os dados simplificado no Anexo VIII
     * @return array
     */
    public function getDespesasComManutencaoDesenvolvimentoEnsinoNovo()
    {
        $filtros = [
            'codigo_relatorio' => AnexoOitoFactory::getCodigoRelatorio($this->iAnoUsu),
            'periodo' => $this->oPeriodo->getCodigo(),
            'DB_anousu' => $this->iAnoUsu,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoOitoFactory::getService($this->iAnoUsu, $filtros);
        return $service->processaLinhasSimplificado();
    }

    /**
     * @return array
     * @throws ParameterException
     * @throws \BusinessException
     */
    public function getReceitasOperacoesCreditoDespesasCapital()
    {
        $oRelatorio = AnexoRREOFactory::getAnexoRREO(
            AnexoRREOFactory::ANEXO_IX,
            $this->iAnoUsu,
            new Periodo($this->iCodigoPeriodo)
        );
        $sInstituicoes = $this->getInstituicoes();
        $oRelatorio->setInstituicoes($sInstituicoes);
        $aDados = $oRelatorio->getDadosSimplificado();

        $this->aLinhasConsistencia[43]->valor_apurado_ate_bimestre = $aDados[0]->nReceitasRealizadas;
        $this->aLinhasConsistencia[43]->saldo_nao_realizado = $aDados[0]->nSaldoNaoRealizado;
        $this->aLinhasConsistencia[44]->valor_apurado_ate_bimestre = $aDados[1]->nDespesasEmpenhadas;
        $this->aLinhasConsistencia[44]->saldo_nao_realizado = $aDados[1]->nSaldoNaoExecutado;

        return array(
            $this->aLinhasConsistencia[43],
            $this->aLinhasConsistencia[44],
        );
    }

    /**
     * @return array
     */
    public function getProjecaoAtuarialRegimesPrevidencia()
    {

        if (!isset($this->aLinhasConsistencia[45]->exercicio) ||
            !isset($this->aLinhasConsistencia[46]->exercicio)) {
            $this->aLinhasConsistencia[45]->exercicio = 0;
            $this->aLinhasConsistencia[45]->exercicio_10 = 0;
            $this->aLinhasConsistencia[45]->exercicio_20 = 0;
            $this->aLinhasConsistencia[45]->exercicio_35 = 0;

            $this->aLinhasConsistencia[46]->exercicio = 0;
            $this->aLinhasConsistencia[46]->exercicio_10 = 0;
            $this->aLinhasConsistencia[46]->exercicio_20 = 0;
            $this->aLinhasConsistencia[46]->exercicio_35 = 0;
        }

        return array(
            $this->aLinhasConsistencia[45],
            $this->aLinhasConsistencia[46],
            $this->aLinhasConsistencia[47],
            $this->aLinhasConsistencia[48],
            $this->aLinhasConsistencia[49],
            $this->aLinhasConsistencia[50],
            $this->aLinhasConsistencia[51],
            $this->aLinhasConsistencia[52],
        );
    }

    /**
     * @return array
     */
    public function getReceitaAlienacaoAtivosAplicacaoRecursos()
    {
        $oRelatorio = AnexoRREOFactory::getAnexoRREO(
            AnexoRREOFactory::ANEXO_XI,
            $this->iAnoUsu,
            new Periodo($this->iCodigoPeriodo)
        );

        $sInstituicoes = $this->getInstituicoes();
        $oRelatorio->setInstituicoes($sInstituicoes);
        $aDados = $oRelatorio->getDadosSimplificado();

        $this->aLinhasConsistencia[53]->valor_apurado_ate_bimestre = $aDados[0]->nAteBimestre;
        $this->aLinhasConsistencia[53]->saldo_realizar = $aDados[0]->nSaldoRealizar;
        $this->aLinhasConsistencia[54]->valor_apurado_ate_bimestre = $aDados[1]->nAteBimestre;
        $this->aLinhasConsistencia[54]->saldo_realizar = $aDados[1]->nSaldoRealizar;

        return array(
            $this->aLinhasConsistencia[53],
            $this->aLinhasConsistencia[54],
        );
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDespesasAcoesServicosPublicosSaude()
    {
        $oAnexoXII = FactoryAnexoXII::getInstance($this->iAnoUsu, $this->iCodigoPeriodo);
        $oAnexoXII->setInstituicoes($this->getInstituicoes());
        $dadosSimplificado = $oAnexoXII->getDadosSimplificado();
        $this->aLinhasConsistencia[55]->valor_apurado_ate_bimestre = $dadosSimplificado->nTotalDespesasSaudeComImpostos;
        $this->aLinhasConsistencia[55]->minimo_aplicar_exercicio = $dadosSimplificado->nPercentualMinimoAplicar;
        $this->aLinhasConsistencia[55]->aplicado_ate_bimestre = $dadosSimplificado->nPercentualDespesasSaudeComImpostos;

        return array($this->aLinhasConsistencia[55]);
    }

    public function getDespesasAcoesServicosPublicosSaudeNovo()
    {
        $filtros = [
            'codigo_relatorio' => AnexoDozeFactory::getCodigoRelatorio($this->iAnoUsu),
            'periodo' => $this->oPeriodo->getCodigo(),
            'DB_anousu' => $this->iAnoUsu,
            'DB_instit' => db_getsession('DB_instit'),
        ];
        $service = AnexoDozeFactory::getService($this->iAnoUsu, $filtros);
        $dadosSimplificado = $service->simplificado();

        $dadosSimplificado->valor_apurado_ate_bimestre = $dadosSimplificado->valor_apuracao;
        $dadosSimplificado->minimo_aplicar_exercicio = $dadosSimplificado->percentual_minimo;
        $dadosSimplificado->aplicado_ate_bimestre = $dadosSimplificado->percentual_aplicado;
        $dadosSimplificado->totalizar = false;
        $dadosSimplificado->nivel = 1;

        return [$dadosSimplificado];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDespesasCaraterContinuadoDerivadasPPP()
    {
        $relatorio = FactoryAnexoXIII::getInstance($this->getAno(), $this->getPeriodo()->getCodigo());
        $relatorio->setInstituicoes($this->getInstituicoes());

        $totalDespesasRCL = $relatorio->getDadosSimplificado();

        $this->aLinhasConsistencia[56]->valor_apurado_ate_bimestre = $totalDespesasRCL->exercicio_corrente;

        return array($this->aLinhasConsistencia[56]);
    }

    /**
     * @throws Exception
     */
    public function getReceitaCorrenteLiquidaNovo()
    {
        $filtros = [
            'codigo_relatorio' => AnexoTresFactory::getCodigoRelatorio($this->iAnoUsu),
            'periodo' => AnexoTresFactory::transformPeriodo($this->oPeriodo->getCodigo()),
            'DB_anousu' => $this->iAnoUsu,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoTresFactory::getService($this->iAnoUsu, $filtros);
        return $service->processaLinhasSimplificado();
    }
}
