<?php

namespace App\Domain\Tributario\ISSQN\Services\Redesim;

use App\Domain\Patrimonial\Ouvidoria\Services\AcaoProcessoService;
use App\Domain\Patrimonial\Ouvidoria\Services\AtendimentoProcessoService;
use App\Domain\Patrimonial\Ouvidoria\Services\CidadaoCgmLegacyService;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoPadrao;
use App\Domain\Tributario\ISSQN\Model\Redesim\InscricaoRedesim;
use App\Domain\Tributario\ISSQN\Services\Redesim\InclusaoEmpresa\AtendimentoInclusaoInscricaoJsonService;
use BusinessException;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Patrimonial\Ouvidoria\Externa\WebService\ProcessoEletronico\Solicitacao;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Filter\ListagemProcessos as FiltroProcesso;

class RedesimBalcaoUnicoService
{
    /**
     * @throws \ParameterException
     * @throws BusinessException
     * @throws \Exception
     */
    public function incluirInscricaoBalcaoUnico(AtendimentoProcessoService $atendimentoProcessoService, $oDados)
    {
        $inscricaoRedesim = InscricaoRedesim::query()
                                            ->where("q179_identificadorredesim", $oDados->identificador)
                                            ->first();

        if ($inscricaoRedesim) {
            return (string) $inscricaoRedesim->q179_inscricao;
        }

        $codigoTipoProcesso = AtendimentoInclusaoInscricaoJsonService::getTipoProcesso();

        $this->initSession($codigoTipoProcesso);

        $atendimentoInclusaoInscricaoJsonService = new AtendimentoInclusaoInscricaoJsonService();
        $sMetadados = $atendimentoInclusaoInscricaoJsonService->setDados($oDados)->build()->toJson();

        $oAtendimento = new Solicitacao();
        $oAtendimento->setMetadados($sMetadados);
        $oAtendimento->setCodigoDepartamento(\db_getsession(DefaultSession::DB_CODDEPTO));
        $oAtendimento->setTipoProcesso($codigoTipoProcesso);
        $oAtendimento->setRequerenteNome("ANONIMO");
        $oAtendimento->setRequerenteCpf(null);
        $oAtendimento->setCodigoAtendimentoAnterior(null);
        $oAtendimento->setClientAPPAtendimentoID(null);
        $oDadosAtendimento = $oAtendimento->salvar();

        $aDados = $this->aprovarAtendimento(
            $atendimentoProcessoService,
            $oDadosAtendimento->atendimento->sequencial,
            $oDados->identificador
        );

        $this->salvarDadosRedesim($aDados["numeroInscricao"], $aDados["numeroProcesso"], (array) $oDados);

        return $aDados["numeroInscricao"];
    }

    /**
     * @throws \Exception
     */
    private function aprovarAtendimento(
        AtendimentoProcessoService $atendimentoProcessoService,
        $iSequencialAtendimento,
        $identificadorRedesim
    ) {
        $filtroProcesso = new FiltroProcesso();
        $filtroProcesso->setSequencial($iSequencialAtendimento);

        $oAtendimento = $atendimentoProcessoService->buscarSolicitacaoOuvidoria($filtroProcesso, true);
        $oAtendimento->metadados = \JSON::create()->parse($oAtendimento->metadados);

        $oCgmResponsavel = $atendimentoProcessoService->getCgmResponsavelByMetadados($oAtendimento->metadados);
        $cgm = (new CidadaoCgmLegacyService())->getCgmBySolicitacao($oAtendimento);

        if (!$oCgmResponsavel) {
            $oCgmResponsavel = $cgm;
        }

        $atendimentoProcessoService->aprovarProcessoSemCapa(
            $cgm,
            $oAtendimento,
            $oCgmResponsavel,
            "Processo gerado a partir do balcão único da REDESIM."
        );

        $atendimentoProcessoService->baixarProcesso("Baixa automática de processo gerado a partir da REDESIM");

        $acaoProcessoService = new AcaoProcessoService();
        $acaoProcessoService->setSolicitacao($oAtendimento)
                            ->setProcesso($atendimentoProcessoService->getProcesso())
                            ->executa();

        $aDados = $acaoProcessoService->getDadosRetorno();

        $this->salvarInscricaoRedesim(
            $aDados["numeroInscricao"],
            $atendimentoProcessoService->getProcesso()->p58_codproc,
            $identificadorRedesim
        );

        return $aDados;
    }

    private function salvarInscricaoRedesim($iInscricao, $iProcesso, $sIdentificadorRedesim)
    {
        $clinscricaoredesim = new \cl_inscricaoredesim();

        $clinscricaoredesim->q179_inscricao = $iInscricao;
        $clinscricaoredesim->q179_processo = $iProcesso;
        $clinscricaoredesim->q179_identificadorredesim = $sIdentificadorRedesim;
        $clinscricaoredesim->incluir();

        if ($clinscricaoredesim->erro_status == "0") {
            throw new \Exception($clinscricaoredesim->erro_msg);
        }
    }

    /**
     * @throws BusinessException
     */
    private function initSession($iTipoProcesso)
    {
        $andamentoPadrao = AndamentoPadrao::ordem(1)->tipoProcesso($iTipoProcesso)->first();

        if (!$andamentoPadrao) {
            throw new BusinessException("Andamento do processo de ordem 1 (um) não configurado.");
        }

        DefaultSession::getInstance()->set(DefaultSession::DB_CODDEPTO, $andamentoPadrao->p53_coddepto);
        \db_putsession(DefaultSession::DB_CODDEPTO, $andamentoPadrao->p53_coddepto);
        DatabaseSession::getInstance()->addSessionToDatabase();
    }

    private function salvarDadosRedesim($iInscricao, $iProcesso, $aDados)
    {
        $cl_dadosredesim = new \cl_dadosredesim();

        $cl_dadosredesim->q181_inscricao = $iInscricao;
        $cl_dadosredesim->q181_processo = $iProcesso;
        $cl_dadosredesim->q181_dadosbalcaounico = addslashes(json_encode($aDados));
        $cl_dadosredesim->incluir();

        if ($cl_dadosredesim->erro_status == "0") {
            throw new \Exception($cl_dadosredesim->erro_msg);
        }
    }
}
