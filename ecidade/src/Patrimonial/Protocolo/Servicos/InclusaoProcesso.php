<?php

namespace ECidade\Patrimonial\Protocolo\Servicos;

use BusinessException;
use \processoProtocolo;

// /var/www/e-cidade/prot1_cadgeralmunic.RPC.php
// incluirAlterar

/**
 * Class InclusaoCgmLegacy
 * @package ECidade\Patrimonial\Protocolo\Servicos
 */
class InclusaoProcesso
{

    /**
     * @throws \Exception
     */
    public function aprovarProcesso(
        \CgmBase $cgm,
        $sequencialProcessoOuvidoria,
        $codigoTipoProcesso,
        $nomeRequerente = null
    ) {
        $oProcesso = $this->incluirProcesso($cgm, $codigoTipoProcesso, $nomeRequerente);
        $this->andamentoProcesso($oProcesso, 'Tramite Inicial');
        $this->vincularProcessoAAtendimento($oProcesso, $sequencialProcessoOuvidoria);
        return $oProcesso;
    }

    /**
     * @throws \Exception
     */
    public function rejeitarProcesso(
        \CgmBase $cgm,
        $sequencialProcessoOuvidoria,
        $codigoTipoProcesso,
        $motivo,
        $nomeRequerente = null
    ) {
        $oProcesso = $this->incluirProcesso($cgm, $codigoTipoProcesso, $nomeRequerente);
        $this->andamentoProcesso($oProcesso, 'Tramite Inicial');
        $this->vincularProcessoAAtendimento($oProcesso, $sequencialProcessoOuvidoria);
        $this->baixarProcesso($oProcesso, $motivo);
        return $oProcesso;
    }

    protected function incluirProcesso(\CgmBase $cgm, $codigoTipoProcesso, $nomeRequerente = null)
    {
        if (empty($nomeRequerente)) {
            $nomeRequerente = $cgm->getNome();
        }

        $oProcesso = new processoProtocolo(null);

        $oProcesso->setTipoProcesso($codigoTipoProcesso);
        $oProcesso->setInterno('false');
        $oProcesso->setPublico('true');
        $oProcesso->setCgm($cgm->getCodigo());
        $oProcesso->setDespacho('');
        $oProcesso->setObservacao('Fluxo Alvará Online');
        $oProcesso->setAnoProcesso(db_getsession("DB_anousu"));
        $oProcesso->setRequerente(substr($nomeRequerente, 0, 79));

        $oProcesso->salvar();

        return $oProcesso;
    }

    public function andamentoProcesso(processoProtocolo $oProcesso, $despacho)
    {
        // $clWorkFlowAtivExec = new \cl_workflowativexec();
        // $clWorkFlowAtivExec->db113_workflowativ = self::ATIVIDADE_INICIAL;
        // $clWorkFlowAtivExec->db113_id_usuario   = db_getsession('DB_id_usuario');
        // $clWorkFlowAtivExec->db113_dtexecucao   = date('Y-m-d', db_getsession('DB_datausu'));
        // $clWorkFlowAtivExec->db113_obs          = $despacho;
        // $clWorkFlowAtivExec->db113_concluido    = 'true';
        // $clWorkFlowAtivExec->incluir(null);

        // if ($clWorkFlowAtivExec->erro_status == 0) {
        //     throw new \Exception($clWorkFlowAtivExec->erro_msg);
        // }

        $iUsuario          = db_getsession('DB_id_usuario');
        $iCodTransferencia = $oProcesso->transferirPorAndamentoPadrao();
        $iProximoDepto     = $oProcesso->getProximoDeptoAndamentoPadrao();
        $iCodRecebimento   = $oProcesso->receber(
            $iCodTransferencia,
            $iProximoDepto,
            db_getsession('DB_id_usuario'),
            $despacho
        );

        // $clProcTransferWorkFlowAtivExec = new \cl_proctransferworkflowativexec();
        // $clProcTransferWorkFlowAtivExec->p46_proctransfer     = $iCodTransferencia;
        // $clProcTransferWorkFlowAtivExec->p46_workflowativexec = $clWorkFlowAtivExec->db113_sequencial;
        // $clProcTransferWorkFlowAtivExec->incluir(null);

        // if ($clProcTransferWorkFlowAtivExec->erro_status == 0) {
        //     throw new \Exception($clProcTransferWorkFlowAtivExec->erro_msg);
        // }
    }

    protected function vincularProcessoAAtendimento(processoProtocolo $oProcesso, $iProcessoOuvidoria)
    {
        $clproceouvidoria = new \cl_processoouvidoria;
        $clproceouvidoria->ov09_ouvidoriaatendimento = $iProcessoOuvidoria;
        $clproceouvidoria->ov09_protprocesso         = $oProcesso->getCodProcesso();
        $clproceouvidoria->ov09_principal            = 'false';
        $clproceouvidoria->incluir(null);

        if ($clproceouvidoria->erro_status == 0) {
            throw new \Exception($clproceouvidoria->erro_msg);
        }
    }

    protected function baixarProcesso($oProcesso, $motivo)
    {
        $oProcesso->arquivar($motivo);
    }
}
