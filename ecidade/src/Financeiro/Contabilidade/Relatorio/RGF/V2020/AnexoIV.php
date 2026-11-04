<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2020;

use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres\AnexoTresService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresInRsService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresMdfService;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2018\AnexoIV as AnexoIV2018;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoIII as AnexoIIIFactory;
use Exception;
use InstituicaoRepository;

class AnexoIV extends AnexoIV2018
{
    /**
     * Ajustamos os dados.
     *
     * @return array|void
     *
     * @throws \Exception
     */
    public function getDados($trazerConfiguracaoPadrao = true)
    {
        if (empty($this->aLinhasConsistencia)) {
            $this->aLinhasConsistencia = $this->getLinhasRelatorio();

            $this->adicionarLinhasRcl();
            ksort($this->aLinhasConsistencia);
            $this->executarBalancetesNecessarios();
            $this->processarValoresManuais();
        }
        $this->ajustarLabelRelatorios();

        return $this->aLinhasConsistencia;
    }

    /**
     * @return array
     *               Retorna as linhas usadas no SIGAP Fiscal XML
     */
    public function getLinhas()
    {
        return $this->aLinhasConsistencia;
    }

    /**
     * calcula as linhas da RCO.
     *
     * @throws \Exception
     */
    protected function processaLinhasQuadro2()
    {
        $linhas = $this->aLinhasConsistencia;
        $this->calcularRCL();

        $valorRclAjustada = $linhas['18.2']->valor;

        $linhas[19]->percentual = 0;
        $linhas[20]->percentual = 0;
        $linhas[23]->percentual = 0;

        $linhas[20]->valor = round(
            ($linhas[17]->ateperiodo + $linhas[19]->valor -
                $linhas[10]->ateperiodo - $linhas[16]->ateperiodo),
            2
        );

        $linhas[21]->valor = round(($linhas['18.2']->valor * 0.16), 2);
        $linhas[22]->valor = round(($linhas['18.2']->valor * 14.4) / 100, 2);
        $linhas[24]->valor = round($linhas['18.2']->valor * 0.07, 2);

        $linhas[21]->percentual = 16;
        $linhas[22]->percentual = 14.4;
        $linhas[24]->percentual = 7;
        if ($valorRclAjustada > 0) {
            $linhas[19]->percentual = round((($linhas[19]->valor / $valorRclAjustada) * 100), 2);
            $linhas[20]->percentual = round((($linhas[20]->valor / $valorRclAjustada) * 100), 2);
            $linhas[23]->percentual = round((($linhas[23]->valor) / $valorRclAjustada) * 100, 2);
        }
    }

    protected function calcularRCL()
    {
        if ($this->iAno <= 2020) {
            $this->processaRClAntiga();
        } else {
            $this->processaRClNova();
        }
    }

    /**
     * Adiciona as linhas novas da RCL.
     */
    protected function adicionarLinhasRcl()
    {
        $linhaDivida = new \stdClass();
        $linhaDivida->ordem = 18.1;
        $linhaDivida->codigo = 0;
        $linhaDivida->totalizar = false;
        $linhaDivida->lMultiCell = true;
        $linhaDivida->descricao = '(-) Transferências obrigatórias da União relativas às emendas individuais ';
        $linhaDivida->descricao .= '(art. 166-A, § 1o, da CF) (V)';
        $linhaDivida->colunas = [];
        $linhaDivida->contas = [];
        $linhaDivida->desdobrar = false;
        $linhaDivida->nivel = 2;
        $linhaDivida->parametros = [];
        $linhaDivida->oLinhaRelatorio = new \linhaRelatorioContabil(0, 0);
        $linhaDivida->origem = 0;

        $this->aLinhasConsistencia['18.1'] = $linhaDivida;

        $linhaDivida = new \stdClass();
        $linhaDivida->ordem = 18.2;
        $linhaDivida->codigo = 0;
        $linhaDivida->lMulticell = true;
        $linhaDivida->totalizar = false;
        $linhaDivida->descricao = 'RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DE ENDIVIDAMENTO ';
        $linhaDivida->descricao .= '(VI) = (IV - V)';
        $linhaDivida->colunas = [];
        $linhaDivida->contas = [];
        $linhaDivida->desdobrar = false;
        $linhaDivida->nivel = 2;
        $linhaDivida->parametros = [];
        $linhaDivida->oLinhaRelatorio = new \linhaRelatorioContabil(0, 0);
        $linhaDivida->origem = 0;

        $this->aLinhasConsistencia['18.2'] = $linhaDivida;
    }

    protected function ajustarLabelRelatorios()
    {
        $linhas = $this->aLinhasConsistencia;
        $linhas[19]->descricao = 'OPERAÇÕES VEDADAS (VII)';
        $linhas[20]->descricao = 'TOTAL CONSIDERADO PARA FINS DA APURAÇÃO DO CUMPRIMENTO DO LIMITE (VIII) = ';
        $linhas[20]->descricao .= '(IIIa + VII - Ia - IIa)';
    }

    /**
     * retorna os dados para o anexo Simplificado.
     *
     * @return \stdClass
     */
    public function getDadosSimplificado()
    {
        /*
         * Carrega as informações que usaremos abaixo
         */
        $this->processar();

        $oStdOperacaoCredito = new \stdClass();
        $nValorReceitaCorrenteLiquida = $this->aLinhasConsistencia['18.2']->valor;
        $calculo = (($this->aLinhasConsistencia[17]->ateperiodo / $nValorReceitaCorrenteLiquida) * 100);
        $nPercentualTotalOperacoesCredito = $calculo;
        $oStdOperacaoCredito->total_operacoes_credito = $this->aLinhasConsistencia[17]->ateperiodo;
        $oStdOperacaoCredito->perc_operacoes_credito = round($nPercentualTotalOperacoesCredito, 2);

        $oStdOperacaoCredito->total_antecipacao_receita_orcamentaria = $this->aLinhasConsistencia[23]->valor;
        $oStdOperacaoCredito->perc_antecipacao_receita_orcamentaria = $this->aLinhasConsistencia[23]->percentual;

        $oStdOperacaoCredito->total_credito_interna_externa = $this->aLinhasConsistencia[21]->valor;
        $oStdOperacaoCredito->perc_credito_interna_externa = $this->aLinhasConsistencia[21]->percentual;

        $oStdOperacaoCredito->total_credito_interna_receita_orcamentaria = $this->aLinhasConsistencia[24]->valor;
        $oStdOperacaoCredito->perc_credito_interna_receita_orcamentaria = $this->aLinhasConsistencia[24]->percentual;

        return $oStdOperacaoCredito;
    }

    private function processaRClAntiga()
    {
        $periodoParaRCl = $this->getPeriodo()->getCodigo();
        $instituicoes = InstituicaoRepository::getInstituicoes();
        $codigoInstituicoes = implode(',', array_keys($instituicoes));
        $dadosAnexoIII = AnexoIIIFactory::getInstance($this->iAnoUsu, $periodoParaRCl);
        $dadosAnexoIII->setInstituicoes($codigoInstituicoes);
        $dadosRcl = $dadosAnexoIII->getDadosSimplificado();
        $valorRcl = $dadosRcl->valor_rcl_mdf;
        $valorRclTransferenciaIndividual = $dadosRcl->valor_rcl_transferencia_individual;
        $valorRclAjustada = $dadosRcl->valor_rcl_endividamento;
        $linhas = $this->aLinhasConsistencia;
        $linhas[18]->valor = $valorRcl;
        $linhas['18.1']->valor = $valorRclTransferenciaIndividual;
        $linhas['18.2']->valor = $valorRclAjustada;
        $linhas[18]->percentual = '-';
        $linhas['18.1']->percentual = '-';
        $linhas['18.2']->percentual = '-';
    }

    /**
     * @return void
     * @throws Exception
     */
    private function processaRClNova()
    {
        $serviceRCL = $this->getServiceNovoRCL($this->getPeriodo()->getCodigo());

        $simplificado = $serviceRCL->processaLinhasSimplificado();
        $valorRCL = $simplificado[0]->ate_bimestre;
        $linhaEmendaIndividuais = $serviceRCL->getLinhaEmendaIndividuais();

        $linhas = $this->aLinhasConsistencia;

        $linhas[18]->valor = $valorRCL;
        $linhas['18.1']->valor = $linhaEmendaIndividuais->total_meses;
        $linhas['18.2']->valor = $simplificado[1]->ate_bimestre;
        $linhas[18]->percentual = '-';
        $linhas['18.1']->percentual = '-';
        $linhas['18.2']->percentual = '-';
    }

    /**
     * @param $codigoPeriodo
     * @return AnexoTresService
     * @throws Exception
     */
    private function getServiceNovoRCL($codigoPeriodo)
    {
        $filtros = [
            'codigo_relatorio' => AnexoTresFactory::getCodigoRelatorio($this->getAno()),
            'periodo' => AnexoTresFactory::transformPeriodo($codigoPeriodo),
            'DB_anousu' => $this->getAno(),
            'DB_instit' => db_getsession('DB_instit')
        ];

        return AnexoTresFactory::getService($this->getAno(), $filtros);
    }
    
    public function getLinhasProcessadas()
    {
        $this->processar();
        return $this->aLinhasConsistencia;
    }
}
