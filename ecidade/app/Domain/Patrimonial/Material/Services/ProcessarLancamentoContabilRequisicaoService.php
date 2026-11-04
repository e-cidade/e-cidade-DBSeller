<?php

namespace App\Domain\Patrimonial\Material\Services;

use BusinessException;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use EventoContabil;
use LancamentoAuxiliarMovimentacaoEstoque;
use SingletonRegraDocumentoContabil;

class ProcessarLancamentoContabilRequisicaoService
{
    public function __construct($dadosRequisicao)
    {
        $this->requisicaoMaterial = $dadosRequisicao->requisicaoMaterial;
        $this->codigoMaterial = $dadosRequisicao->codigoMaterial;
        $this->codigoLancamentoMaterial = $dadosRequisicao->codigoLancamentoMaterial;
        $this->valorAtendido = $dadosRequisicao->valorAtendido;
        $this->session = $dadosRequisicao->session;
    }

    public function toStd()
    {
        return (object)[
            'requisicaoMaterial' => $this->requisicaoMaterial,
            'codigoMaterial' => $this->codigoMaterial,
            'codigoLancamentoMaterial' => $this->codigoLancamentoMaterial,
            'valorAtendido' => $this->valorAtendido,
            'session' => $this->session,
        ];
    }

    public function processar()
    {
        require_once(modification("classes/materialestoque.model.php"));
        $_SESSION = $this->session;
        DefaultSession::getInstance()->addFromRequest($this->session);
        DatabaseSession::getInstance()->addSessionToDatabase();
        $oMaterialEstoque = new \materialEstoque($this->codigoMaterial);
        if (empty($this->valorAtendido) || $this->valorAtendido == 0) {
            throw new BusinessException("Valor do lancamento não informado ou igual a 0 !");
        }

        $dtLancamento = date("Y-m-d", db_getsession("DB_datausu"));

        $oEventoContabil = new EventoContabil(400, db_getsession("DB_anousu"));
        $aLancamentos = $oEventoContabil->getEventoContabilLancamento();
        if (count($aLancamentos) == 0) {
            $sMensagem = "Não existe lançamentos para o evento 400 - {$oEventoContabil->getDescricaoDocumento()}";
            throw new BusinessException($sMensagem);
        }

        $iCodigoHistorico = $aLancamentos[0]->getHistorico();
        $oLancamentoAuxiliarEstoque = new LancamentoAuxiliarMovimentacaoEstoque();
        $oLancamentoAuxiliarEstoque->setCodigoMovimentacaoEstoque($this->codigoLancamentoMaterial);
        $oLancamentoAuxiliarEstoque->setValorTotal($this->valorAtendido);
        $sHistoricoLancamento = sprintf(
            "Lançamento contábil referente a atendimento da requisição %s, material %s.",
            $this->requisicaoMaterial,
            $oMaterialEstoque->getcodMater()
        );

        $oLancamentoAuxiliarEstoque->setObservacaoHistorico($sHistoricoLancamento);
        $oLancamentoAuxiliarEstoque->setHistorico($iCodigoHistorico);
        $oLancamentoAuxiliarEstoque->setMaterial($oMaterialEstoque);
        $oLancamentoAuxiliarEstoque->setSaida(true);
        if ($oMaterialEstoque->getGrupo() != null) {
            $oLancamentoAuxiliarEstoque->setContaPcasp($oMaterialEstoque->getGrupo()->getConta());
        }

        $this->executarLancamentosContabeis(400, $oLancamentoAuxiliarEstoque, $dtLancamento);
    }

    /**
     * @throws BusinessException
     * @throws \Exception
     */
    protected function executarLancamentosContabeis(
        $iCodigoDocumento,
        LancamentoAuxiliarMovimentacaoEstoque $oLancamentoAuxiliar,
        $dtLancamento
    ) {
        $oDocumentoContabil = SingletonRegraDocumentoContabil::getDocumento($iCodigoDocumento);
        $iCodigoDocumentoExecutar = $oDocumentoContabil->getCodigoDocumento();
        $oEventoContabil = new EventoContabil($iCodigoDocumentoExecutar, db_getsession("DB_anousu"));
        if ($iCodigoDocumentoExecutar == 401) {
            $oLancamentoAuxiliar->setSaida(false);
        } else {
            $oLancamentoAuxiliar->setSaida(true);
        }

        $oEventoContabil->executaLancamento($oLancamentoAuxiliar, $dtLancamento);
    }
}
