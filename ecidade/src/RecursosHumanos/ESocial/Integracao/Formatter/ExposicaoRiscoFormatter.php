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

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use DBDate;
use DBException;
use ECidade\RecursosHumanos\Pessoal\Model\AgenteNocivo;
use ECidade\RecursosHumanos\Pessoal\Model\EquipamentoProtecao;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorMovimentacao;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use ECidade\RecursosHumanos\Pessoal\Servidor\Model\Cargo;
use ECidade\RecursosHumanos\Pessoal\Servidor\Model\Funcao;
use stdClass;

class ExposicaoRiscoFormatter extends Formatter
{
    /**
     * @var \Servidor
     */
    private $dadoAtual;

    /**
     * @var \LocalTrabalho
     */
    private $localAtual;

    /**
     * @var ServidorMovimentacao|null
     */
    private $movimentacaoAtual;
    /**
     * @var string
     */
    private $inscricaoEmpregador;
    /**
     * @var int
     */
    private $ano;

    /**
     * @var int
     */
    private $mes;

    private $dataAdmicao;

    private $agenteDescricao = [
        "01.01.001",
        "01.02.001",
        "01.03.001",
        "01.04.001",
        "01.05.001",
        "01.06.001",
        "01.07.001",
        "01.08.001",
        "01.09.001",
        "01.10.001",
        "01.12.001",
        "01.13.001",
        "01.14.001",
        "01.15.001",
        "01.16.001",
        "01.17.001",
        "01.18.001",
        "05.01.001"
    ];
    /**
     * @param array $dados
     * @return array|\Servidor[]
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $dataInicioDefault = \DBPessoal::getDataFaseEsocial(4);
        
        $this->inscricaoEmpregador = $dados->inscricao_empregador;
        $dadosFormatados = [];
        foreach ($dados->servidores as $dado) {
            $this->dataAdmicao = $dado->getDataAdmissao()->getDate();
            $this->dadoAtual = $dado;
            $this->movimentacaoAtual = ServidorMovimentacao::fromState(
                ['rh02_seqpes' => $this->dadoAtual->getCodigoMovimentacao()]
            );
            // Pegamos os locais de Trabalho e enviamos somente o principal
            foreach ($this->dadoAtual->getLocaisTrabalho() as $local) {
                if ($local->isPrincipal()) {
                    $this->localAtual = $local;
                    $dadosFormatados[] = $this->montarDados();
                }
            }
        }
        return $dadosFormatados;
    }

    private function montarDados()
    {
        $registro = new stdClass();
        $registro->inscricao_empregador = $this->inscricaoEmpregador;
        $registro->referencia = $this->dadoAtual->getMatricula() . "_" . $this->localAtual->getCodigo();
        $registro->ideVinculo = $this->montarVinculo();
        $registro->infoExpRisco = $this->montarInformacoesExposicaoRisco();
        return $registro;
    }

    private function montarVinculo()
    {
        $ideVinculo = new stdClass();
        $ideVinculo->cpfTrab = $this->dadoAtual->getCgm()->getCpf();
        if ($this->dadoAtual->temVinculoEmpregaticio()) {
            $ideVinculo->matricula = $this->dadoAtual->getMatricula();
        } else {
            $ideVinculo->codCateg = $this->dadoAtual->getVinculo()->getCodigoCategoria();
        }
        if ($this->dadoAtual->getMatriculaAnterior() != "") {
            $ideVinculo->matricula = $this->dadoAtual->getMatriculaAnterior();
        }
        return $ideVinculo;
    }

    private function montarInformacoesExposicaoRisco()
    {
        $infoExpRisco = new stdClass();
        $dataInicioLocalAtual = '';
        $dataEsocial = date('Y-m-d', strtotime(\DBPessoal::getDataFaseEsocial(4)));
        $dataAdmicao = $this->dataAdmicao;

        if (!empty($this->localAtual->getDataInicio())) {
            $dataInicioLocalAtual = $this->localAtual->getDataInicio()->getDate();
            if ($dataAdmicao > $dataInicioLocalAtual) {
                $infoExpRisco->dtIniCondicao = $dataEsocial;
            } elseif ($dataEsocial > $dataInicioLocalAtual) {
                $infoExpRisco->dtIniCondicao = $dataEsocial;
            } elseif ($dataAdmicao > $dataEsocial) {
                $infoExpRisco->dtIniCondicao = $dataAdmicao;
            } else {
                if ($dataInicioLocalAtual >= $dataEsocial) {
                    $infoExpRisco->dtIniCondicao = $dataInicioLocalAtual;
                } else {
                    $infoExpRisco->dtIniCondicao = $dataEsocial;
                }
            }
        } else {
            if ($dataAdmicao < $dataEsocial) {
                $infoExpRisco->dtIniCondicao = $dataEsocial;
            } else {
                $infoExpRisco->dtIniCondicao = $dataAdmicao;
            }
        }
     
        $infoExpRisco->infoAmb = $this->montarInformacaoAmbiente();
        $infoExpRisco->infoAtiv = $this->montarInformacaoAtividade();
        $infoExpRisco->agNoc = $this->montarAgentesNocivos();
        $infoExpRisco->respReg = $this->montarResponsavel();
        if (!empty($this->localAtual->getObservacao())) {
            $infoExpRisco->obs = $this->localAtual->getObservacao();
        }
        return $infoExpRisco;
    }

    private function montarInformacaoAmbiente()
    {
        $infoAmb = new stdClass();
        $movimentacao = new ServidorMovimentacaoRepository();
        $this->movimentacaoAtual = $movimentacao->scopeSeqPes($this->dadoAtual->getCodigoMovimentacao())->first();
        $infoAmb->localAmb = $this->dadoAtual->getLocaisTrabalho();
        if ($this->localAtual->getInstituicao()->getCNPJ() == $this->inscricaoEmpregador) {
            $infoAmb->localAmb = 1;
        } else {
            $infoAmb->localAmb = 2;
        }
        $infoAmb->dscSetor = $this->localAtual->getDescricao();
        $infoAmb->tpInsc = 1;
        $infoAmb->nrInsc = $this->localAtual->getInstituicao()->getCNPJ();
        return $infoAmb;
    }


    private function montarInformacaoAtividade()
    {
        $cargo = new Cargo($this->movimentacaoAtual->getFuncao());
        $funcao = new Funcao($this->movimentacaoAtual->getCargo());
        $infoAtiv = new stdClass();
        if (!empty($cargo->getDescricaoAtividade())) {
            $infoAtiv->dscAtivDes = $cargo->getDescricaoAtividade();
        } else {
            $infoAtiv->dscAtivDes = $funcao->getDescricaoAtividade();
        }
        return $infoAtiv;
    }

    private function montarAgentesNocivos()
    {
        $agentes = $this->localAtual->getAgentesNocivos();
        $agNoc = [];
        foreach ($agentes as $ag) {
            $agente = new stdClass();
            $agente->codAgNoc = $ag->getTipoAvaliacao();
            $agente->tpAval = (integer) $ag->getTipoAvaliacao();
            if (empty($agente->tpAval)) {
                unset($agente->tpAval);
            }
            if (!empty($ag->getIntensidadeConcentracao())) {
                $agente->intConc = (integer) $ag->getIntensidadeConcentracao();
            }
            if (!empty($ag->getToleranciaLimite())) {
                $agente->limTol = (integer) $ag->getToleranciaLimite();
            }
            if (!empty($ag->getMedida())) {
                $agente->unMed = (integer) $ag->getMedida();
            }
            if (!empty($ag->getTecnicaMedicao())) {
                $agente->tecMedicao = $ag->getTecnicaMedicao();
            }
            if (!empty($ag->getProcesso())) {
                $agente->nrProcJud = $ag->getProcesso();
            }
            $agente->epcEpi = $this->montarInformacaoEpi($ag);
            $agente->codAgNoc = $ag->getAgente();
            if (in_array($agente->codAgNoc, $this->agenteDescricao)) {
                $agente->dscAgNoc = AgenteNocivo::getDescricaoByCodigo($agente->codAgNoc);
            }
            $agNoc[] = clone $agente;
        }
        return $agNoc;
    }

    private function montarInformacaoEpi(AgenteNocivo $agente)
    {
        $epcEpi = new stdClass();
        $epc = $agente->getEquipamento();
        $epcEpi->utilizEPC = (integer) $epc->getUtilizaEpc();
        if (!empty($epc->getEficaciaEpc())) {
            $epcEpi->eficEpc = $epc->getEficaciaEpc();
        }
        $epcEpi->utilizEPI = (integer) $epc->getUtilizaEpi();
        if (!empty($epc->getEficaciaEpi())) {
            $epcEpi->eficEpi = $epc->getEficaciaEpi();
        }
        if (sizeof($epc->getEpis()) > 0) {
            $epcEpi->epi = $this->montarEpis($epc->getEpis());
        }
        $epiCompl = $this->montarEpcComplementar($epc);
        if (!empty($epiCompl)) {
            $epcEpi->epiCompl = $epiCompl;
        }

        return $epcEpi;
    }

    private function montarEpis($listaEpis)
    {
        $epis = [];
        foreach ($listaEpis as $epi) {
            $registro = new stdClass();
            if (!empty($epi->getDocumentoAvaliacao())) {
                $registro->docAval = $epi->getDocumentoAvaliacao();
            }
            $epis[] = $registro;
        }
        return $epis;
    }

    private function montarEpcComplementar(EquipamentoProtecao $epc)
    {
        $epiCompl = new stdClass();
        $retorno = false;

        if (!empty($epc->getMedidaProtecaoEpi())) {
            $epiCompl->medProtecao = $epc->getMedidaProtecaoEpi();
            $retorno = true;
        }
        if (!empty($epc->getFuncionamentoEpi())) {
            $epiCompl->condFuncto = $epc->getFuncionamentoEpi();
            $retorno = true;
        }
        if (!empty($epc->getUsoInterruptoEpi())) {
            $epiCompl->usoInint = $epc->getUsoInterruptoEpi();
            $retorno = true;
        }
        if (!empty($epc->getValidadeEpi())) {
            $epiCompl->przValid = $epc->getValidadeEpi();
            $retorno = true;
        }
        if (!empty($epc->getPeriodicidadeEpi())) {
            $epiCompl->periodicTroca = $epc->getPeriodicidadeEpi();
            $retorno = true;
        }
        if (!empty($epc->getHigienizacaoEpi())) {
            $epiCompl->higienizacao = $epc->getHigienizacaoEpi();
            $retorno = true;
        }
        if (!$retorno) {
            return $retorno;
        }
        return $epiCompl;
    }

    private function montarResponsavel()
    {
        $respRegs = [];
        $registros = $this->localAtual->getRegistrosAmbientais();
        foreach ($registros as $regitro) {
            $respReg =  new stdClass();
            $respReg->cpfResp = $regitro->getCpf();
            $respReg->ideOC = (integer) $regitro->getIdentificacaoOrgao();
            if (!empty($regitro->getDescricaoOrgao())) {
                $respReg->dscOC = $regitro->getDescricaoOrgao();
            }
            $respReg->nrOC = $regitro->getNumeroInscricaoOrgao();
            $respReg->ufOC = $regitro->getUfOrgao();
            $respRegs[] =  $respReg;
        }
        return $respRegs;
    }

    private function montarObservacao()
    {
        $obsCompl = "";
        return $obsCompl;
    }
}
