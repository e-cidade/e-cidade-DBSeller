<?php
require_once(modification("interfaces/IRegraLancamentoContabil.interface.php"));

class RegraApropriacaoDecimoFerias implements IRegraLancamentoContabil
{
    /**
     * @see IRegraLancamentoContabil::getRegraLancamento()
     */
    public function getRegraLancamento($iCodigoDocumento, $iCodigoLancamento, ILancamentoAuxiliar $oLancamentoAuxiliar)
    {
        $oDaoTransacao = new cl_contranslr;
        $sWhere = "     c45_coddoc      = {$iCodigoDocumento}";
        $sWhere .= " and c45_anousu      = " . db_getsession("DB_anousu");
        $sWhere .= " and c46_seqtranslan = {$iCodigoLancamento}";
        $sSqlTransacao = $oDaoTransacao->sql_query(null, "*", null, $sWhere);
        $rsTransacao = $oDaoTransacao->sql_record($sSqlTransacao);

        if ($oDaoTransacao->numrows > 1) {
            throw new BusinessException("Mais de uma regra cadastrada para o documento {$iCodigoDocumento}.");
        }

        /**
         * Nao encontrou regra de lancamento para o documento
         */
        if ($oDaoTransacao->numrows == 0) {
            return false;
        }
        $oDadosTransacao = db_utils::fieldsMemory($rsTransacao, 0);
        $regra = new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);
        $regra->setContaCredito($oLancamentoAuxiliar->getContaCredito());
        $regra->setContaDebito($oLancamentoAuxiliar->getContaDebito());
        return $regra;
    }
}
