<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2020;

use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres\AnexoTresService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresInRsService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresMdfService;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2018\AnexoIII as AnexoIII2018;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoIII as AnexoIIIFactory;
use Exception;
use InstituicaoRepository;
use Periodo;

class AnexoIII extends AnexoIII2018
{
    /**
     * @return array
     * @throws \BusinessException
     * @throws \ParameterException
     */
    public function getDados($trazerConfiguracaoPadrao = true)
    {
        if (!$this->relatorioProcessado) {
            $this->aLinhasConsistencia = $this->getLinhasRelatorio();
            $this->executarBalancetesNecessarios();
            $this->processarValoresManuais();
            $this->processaTotalizadores($this->aLinhasConsistencia);
            $this->adicionarLinhasRcl();
            ksort($this->aLinhasConsistencia);
            if ($this->getPeriodo()->getCodigo() != 14 && $this->getPeriodo()->getCodigo() != 12) {
                $this->recalcularSaldoExercicio();
            }

            $this->calcularRCL();
            $this->processarFormulasLinhasTotalizadoras();
            $this->calcularTotalGarantiasRCL();
            $this->calcularLimiteResolucaoSenado();
            $this->calcularLimiteAlerta();
            $this->ajustarNomeDasColunas();
            $this->relatorioProcessado = true;
        }

        return $this->aLinhasConsistencia;
    }

    /**
     * Adiciona as linhas expecificas da RCL
     */
    protected function adicionarLinhasRcl()
    {
        $linhaDivida = new \stdClass();
        $linhaDivida->ordem = 12.1;
        $linhaDivida->codigo = 0;
        $linhaDivida->totalizar = false;
        $linhaDivida->lMultiCell = true;
        $linhaDivida->descricao = "(-) Transferências obrigatórias da União relativas às emendas individuais ";
        $linhaDivida->descricao .= "(art. 166-A, § 1o, da CF) (VII)";
        $linhaDivida->colunas = [];
        $linhaDivida->contas = array();
        $linhaDivida->desdobrar = false;
        $linhaDivida->nivel = 2;
        $linhaDivida->parametros = [];
        $linhaDivida->oLinhaRelatorio = null;
        $linhaDivida->origem = null;

        $this->aLinhasConsistencia["12.1"] = $linhaDivida;

        $linhaDivida = new \stdClass();
        $linhaDivida->ordem = 12.2;
        $linhaDivida->codigo = 0;
        $linhaDivida->lMulticell = true;
        $linhaDivida->totalizar = false;
        $linhaDivida->descricao = "RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DE ENDIVIDAMENTO ";
        $linhaDivida->descricao .= "(VIII) = (VI - VII)";
        $linhaDivida->colunas = [];
        $linhaDivida->contas = array();
        $linhaDivida->desdobrar = false;
        $linhaDivida->nivel = 2;
        $linhaDivida->parametros = [];
        $linhaDivida->oLinhaRelatorio = null;
        $linhaDivida->origem = null;

        $this->aLinhasConsistencia["12.2"] = $linhaDivida;
    }

    /**
     * Calcula Total das Garantias RCL.
     */
    protected function calcularTotalGarantiasRCL()
    {
        $linhaReferencia = $this->aLinhasConsistencia["12.2"];
        $linhaGarantias = $this->aLinhasConsistencia[11];
        $linhaTotalGarantias = $this->aLinhasConsistencia[13];
        if (!empty($linhaReferencia->saldo_exercicio_anterior)) {
            $linhaTotalGarantias->saldo_exercicio_anterior = ($linhaGarantias->saldo_exercicio_anterior /
                    $linhaReferencia->saldo_exercicio_anterior) * 100;
        }
        if (!empty($linhaReferencia->semestre_1)) {
            $linhaTotalGarantias->semestre_1 = ($linhaGarantias->semestre_1 / $linhaReferencia->semestre_1) * 100;
        }
        if (!empty($linhaReferencia->semestre_2)) {
            $linhaTotalGarantias->semestre_2 = ($linhaGarantias->semestre_2 / $linhaReferencia->semestre_2) * 100;
        }
        if (!empty($linhaReferencia->ate_1_quadrimestre)) {
            $linhaTotalGarantias->ate_1_quadrimestre = ($linhaGarantias->ate_1_quadrimestre /
                    $linhaReferencia->ate_1_quadrimestre) * 100;
        }
        if (!empty($linhaReferencia->ate_2_quadrimestre)) {
            $linhaTotalGarantias->ate_2_quadrimestre = ($linhaGarantias->ate_2_quadrimestre /
                    $linhaReferencia->ate_2_quadrimestre) * 100;
        }
        if (!empty($linhaReferencia->ate_3_quadrimestre)) {
            $linhaTotalGarantias->ate_3_quadrimestre = ($linhaGarantias->ate_3_quadrimestre /
                    $linhaReferencia->ate_3_quadrimestre) * 100;
        }
    }

    /**
     * Calcula LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL 32%
     */
    protected function calcularLimiteResolucaoSenado()
    {
        $limiteSenado = 0.32;
        $linhaReferencia = $this->aLinhasConsistencia["12.2"];
        $linhaResolucao = $this->aLinhasConsistencia[14];
        $linhaResolucao->saldo_exercicio_anterior = $linhaReferencia->saldo_exercicio_anterior * $limiteSenado;

        if (in_array($this->oPeriodo->getCodigo(), array(12, 13))) {
            $linhaResolucao->semestre_1 = $linhaReferencia->semestre_1 * $limiteSenado;
            $linhaResolucao->semestre_2 = $linhaReferencia->semestre_2 * $limiteSenado;
        } else {
            $linhaResolucao->ate_1_quadrimestre = $linhaReferencia->ate_1_quadrimestre * $limiteSenado;
            if (!isset($linhaReferencia->ate_2_quadrimestre)) {
                $linhaReferencia->ate_2_quadrimestre = 0;
            }
            if (!isset($linhaReferencia->ate_3_quadrimestre)) {
                $linhaReferencia->ate_3_quadrimestre = 0;
            }

            $linhaResolucao->ate_2_quadrimestre = $linhaReferencia->ate_2_quadrimestre * $limiteSenado;
            $linhaResolucao->ate_3_quadrimestre = $linhaReferencia->ate_3_quadrimestre * $limiteSenado;
        }
    }

    /**
     * Calcula LIMITE DE ALERTA (inciso III do §1o do art. 59 da LRF) 28;8%
     */
    protected function calcularLimiteAlerta()
    {
        $nLimiteAlerta = 28.8;
        $linhas = $this->aLinhasConsistencia;
        $linhas[15]->saldo_exercicio_anterior = ($linhas["12.2"]->saldo_exercicio_anterior * $nLimiteAlerta) / 100;

        if (in_array($this->oPeriodo->getCodigo(), array(12, 13))) {
            $linhas[15]->semestre_1 = ($linhas["12.2"]->semestre_1 * $nLimiteAlerta) / 100;
            $linhas[15]->semestre_2 = ($linhas["12.2"]->semestre_2 * $nLimiteAlerta) / 100;
        } else {
            $linhas[15]->ate_1_quadrimestre = ($linhas["12.2"]->ate_1_quadrimestre * $nLimiteAlerta) / 100;
            $linhas[15]->ate_2_quadrimestre = ($linhas["12.2"]->ate_2_quadrimestre * $nLimiteAlerta) / 100;
            $linhas[15]->ate_3_quadrimestre = ($linhas["12.2"]->ate_3_quadrimestre * $nLimiteAlerta) / 100;
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

    protected function ajustarNomeDasColunas()
    {
        $this->aLinhasConsistencia[13]->descricao = "% do TOTAL DAS GARANTIAS sobre a RCL AJUSTADA (V/VIII)";
        $this->aLinhasConsistencia[16]->descricao = "DOS ESTADOS (IX)";
        $this->aLinhasConsistencia[19]->descricao = "DOS MUNICÍPIOS (X)";
        $this->aLinhasConsistencia[22]->descricao = "DAS ENTIDADES CONTROLADAS (XI)";
        $this->aLinhasConsistencia[25]->descricao = "EM GARANTIAS POR MEIO DE FUNDOS E PROGRAMAS(XII)";
        $this->aLinhasConsistencia[26]->descricao = "TOTAL CONTRAGARANTIAS RECEBIDAS (XIII) = (IX + X + XI + XII)";
    }

    private function processaRClAntiga()
    {
        $linhas = $this->aLinhasConsistencia;
        $instituicoes = InstituicaoRepository::getInstituicoes();
        $codigoInstituicoes = implode(',', array_keys($instituicoes));
        $dadosAnexoXII = AnexoIIIFactory::getInstance($this->iAnoUsu - 1, \Periodo::SEXTO_BIMESTRE);
        $dadosAnexoXII->setInstituicoes($codigoInstituicoes);
        $dadosRclAnoAnterior = $dadosAnexoXII->getDadosSimplificado();
        $linhas[12]->saldo_exercicio_anterior = $dadosRclAnoAnterior->valor_rcl_mdf;
        $linhas["12.1"]->saldo_exercicio_anterior = $dadosRclAnoAnterior->valor_rcl_transferencia_individual;
        $linhas["12.2"]->saldo_exercicio_anterior = $dadosRclAnoAnterior->valor_rcl_endividamento;
        $periodo = $this->oPeriodo->getCodigo();
        foreach ($this->periodos[$periodo] as $codigoPeriodo) {
            $dadosAnexoXII = AnexoIIIFactory::getInstance($this->iAnoUsu, $periodo);
            $dadosAnexoXII->setInstituicoes($codigoInstituicoes);
            $dadosRcl = $dadosAnexoXII->getDadosSimplificado();
            $valorRcl = $dadosRcl->valor_rcl_mdf;
            $valorRclTransferenciaIndividual = $dadosRcl->valor_rcl_transferencia_individual;
            $valorRclAjustada = $dadosRcl->valor_rcl_endividamento;
            switch ($codigoPeriodo) {
                case 12:
                    $linhas[12]->semestre_1 = $valorRcl;
                    $linhas["12.1"]->semestre_1 = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->semestre_1 = $valorRclAjustada;
                    break;
                case 13:
                    $linhas[12]->semestre_2 = $valorRcl;
                    $linhas["12.1"]->semestre_2 = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->semestre_2 = $valorRclAjustada;
                    break;
                case 14:
                    $linhas[12]->ate_1_quadrimestre = $valorRcl;
                    $linhas["12.1"]->ate_1_quadrimestre = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->ate_1_quadrimestre = $valorRclAjustada;
                    break;
                case 15:
                    $linhas[12]->ate_2_quadrimestre = $valorRcl;
                    $linhas["12.1"]->ate_2_quadrimestre = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->ate_2_quadrimestre = $valorRclAjustada;
                    break;
                case 16:
                    $linhas[12]->ate_3_quadrimestre = $valorRcl;
                    $linhas["12.1"]->ate_3_quadrimestre = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->ate_3_quadrimestre = $valorRclAjustada;
                    break;
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function processaRClNova()
    {
        $serviceRCLAnterior = $this->getServiceNovoRCL(Periodo::SEGUNDO_SEMESTRE, $this->getAno() -1);
        $simplificado = $serviceRCLAnterior->processaLinhasSimplificado();
        $linhaEmendaIndividuais = $serviceRCLAnterior->getLinhaEmendaIndividuais();

        $linhas = $this->aLinhasConsistencia;
        $linhas[12]->saldo_exercicio_anterior = $simplificado[0]->ate_bimestre;
        $linhas["12.1"]->saldo_exercicio_anterior = $linhaEmendaIndividuais->total_meses;
        $linhas["12.2"]->saldo_exercicio_anterior = $simplificado[1]->ate_bimestre;

        // inicializa as propriedades
        $linhas[12]->semestre_1 = 0;
        $linhas["12.1"]->semestre_1 = 0;
        $linhas["12.2"]->semestre_1 = 0;
        $linhas[12]->semestre_2 = 0;
        $linhas["12.1"]->semestre_2 = 0;
        $linhas["12.2"]->semestre_2 = 0;
        $linhas[12]->ate_1_quadrimestre = 0;
        $linhas["12.1"]->ate_1_quadrimestre = 0;
        $linhas["12.2"]->ate_1_quadrimestre = 0;
        $linhas[12]->ate_2_quadrimestre = 0;
        $linhas["12.1"]->ate_2_quadrimestre = 0;
        $linhas["12.2"]->ate_2_quadrimestre = 0;
        $linhas[12]->ate_3_quadrimestre = 0;
        $linhas["12.1"]->ate_3_quadrimestre = 0;
        $linhas["12.2"]->ate_3_quadrimestre = 0;

        $periodo = $this->oPeriodo->getCodigo();
        foreach ($this->periodos[$periodo] as $codigoPeriodo) {
            $serviceRCL = $this->getServiceNovoRCL($codigoPeriodo, $this->getAno());
            $simplificado = $serviceRCL->processaLinhasSimplificado();
            $linhaEmendaIndividuais = $serviceRCL->getLinhaEmendaIndividuais();
            $valorRcl = $simplificado[0]->ate_bimestre;
            $valorRclTransferenciaIndividual = $linhaEmendaIndividuais->total_meses;
            $valorRclAjustada = $simplificado[1]->ate_bimestre;

            switch ($codigoPeriodo) {
                case 12:
                    $linhas[12]->semestre_1 = $valorRcl;
                    $linhas["12.1"]->semestre_1 = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->semestre_1 = $valorRclAjustada;
                    break;
                case 13:
                    $linhas[12]->semestre_2 = $valorRcl;
                    $linhas["12.1"]->semestre_2 = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->semestre_2 = $valorRclAjustada;

                    dump($linhas[12], $linhas["12.1"], $linhas["12.2"]);
                    break;
                case 14:
                    $linhas[12]->ate_1_quadrimestre = $valorRcl;
                    $linhas["12.1"]->ate_1_quadrimestre = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->ate_1_quadrimestre = $valorRclAjustada;
                    break;
                case 15:
                    $linhas[12]->ate_2_quadrimestre = $valorRcl;
                    $linhas["12.1"]->ate_2_quadrimestre = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->ate_2_quadrimestre = $valorRclAjustada;
                    break;
                case 16:
                    $linhas[12]->ate_3_quadrimestre = $valorRcl;
                    $linhas["12.1"]->ate_3_quadrimestre = $valorRclTransferenciaIndividual;
                    $linhas["12.2"]->ate_3_quadrimestre = $valorRclAjustada;
                    break;
            }
        }
    }

    /**
     * @param $codigoPeriodo
     * @param $exercicio
     * @return array
     * @throws Exception
     */
    private function getDadosSimplificadoNovoRCL($codigoPeriodo, $exercicio)
    {
        $service = $this->getServiceNovoRCL($codigoPeriodo, $exercicio);

        return $service->processaLinhasSimplificado();
    }

    /**
     * @param $codigoPeriodo
     * @param $exercicio
     * @return AnexoTresService
     * @throws Exception
     */
    private function getServiceNovoRCL($codigoPeriodo, $exercicio)
    {
        $filtros = [
            'codigo_relatorio' => AnexoTresFactory::getCodigoRelatorio($exercicio),
            'periodo' => AnexoTresFactory::transformPeriodo($codigoPeriodo),
            'DB_anousu' => $exercicio,
            'DB_instit' => db_getsession('DB_instit')
        ];

        return AnexoTresFactory::getService($exercicio, $filtros);
    }
}
