<?php

require_once(modification("interfaces/IRegraLancamentoContabil.interface.php"));
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */


/**]
 * Classe responsavel por criar a regra de lancamento de inscricao dos Restos a pagar
 * @author Iuri Guntchnigg
 * @package Contabilidade
 * @subpackage lancamento
 * @version $Revision: 1.3 $
 * Class RegraLancamentoEncerramentoRP
 */
class RegraLancamentoAberturaResto implements IRegraLancamentoContabil
{

    protected $documentosTransferencia = array(2032, 2033);
    /**
     * @param int $iCodigoDocumento
     * @param int $iCodigoLancamento
     * @param ILancamentoAuxiliar| $oLancamentoAuxiliar
     * @return bool|RegraLancamentoContabil
     * @throws Exception
     */
    public function getRegraLancamento($iCodigoDocumento, $iCodigoLancamento,
                                       ILancamentoAuxiliar $oLancamentoAuxiliar)
    {


        if (in_array($iCodigoDocumento, $this->documentosTransferencia)) {


            $oDaoTransacao = new cl_contranslr;
            $sWhere        = "     c45_coddoc      = {$iCodigoDocumento}";
            $sWhere       .= " and c45_anousu      = ".db_getsession("DB_anousu");
            $sWhere       .= " and c46_seqtranslan = {$iCodigoLancamento}";
            $sSqlTransacao = $oDaoTransacao->sql_queryRegraLancamento(null, "*", null, $sWhere);
            $rsTransacao   = $oDaoTransacao->sql_record($sSqlTransacao);

            if ($oDaoTransacao->numrows == 0) {
              $sMsgErro = "Não há lançamentos configurados para o documento {$iCodigoDocumento}.";
              throw new BusinessException($sMsgErro);
            }
            for ($iLinhaRegra = 0; $iLinhaRegra < $oDaoTransacao->numrows; $iLinhaRegra++){

                $oDadosTransacao = db_utils::fieldsMemory($rsTransacao, $iLinhaRegra);

                 $regra = new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);
                 return $regra;

              }

        }



        $oEventoContabil = EventoContabilRepository::getEventoContabilByCodigo($iCodigoDocumento, db_getsession("DB_anousu"));
        $oLancamentoEventoContabil = $oEventoContabil->getEventoContabilLancamentoPorCodigo($iCodigoLancamento);

        if (!$oLancamentoEventoContabil || count($oLancamentoEventoContabil->getRegrasLancamento()) == 0) {
            return false;
        }

        $aRegrasDoLancamento = $oLancamentoEventoContabil->getRegrasLancamento();
        if (count($aRegrasDoLancamento) == 0) {
            return false;
        }

        if (in_array($iCodigoDocumento, $this->documentosTransferencia) && $oLancamentoEventoContabil->getOrdem() == 1) {
            $regrasDoLancamentoAlteradas = clone $aRegrasDoLancamento[0];
            $regrasDoLancamentoAlteradas->setContaCredito($oLancamentoAuxiliar->getContaCredito());
            $regrasDoLancamentoAlteradas->setContaDebito($oLancamentoAuxiliar->getContaDebito());
            return $regrasDoLancamentoAlteradas;
        }
        if (in_array($iCodigoDocumento, $this->documentosTransferencia) &&
            $oLancamentoEventoContabil->getOrdem() > 1 && $oLancamentoAuxiliar->isInversaoContas()) {
            $regrasDoLancamentoAlteradas = clone $aRegrasDoLancamento[0];
            $regrasDoLancamentoAlteradas->setContaCredito($aRegrasDoLancamento[0]->getContaDebito());
            $regrasDoLancamentoAlteradas->setContaDebito($aRegrasDoLancamento[0]->getContaCredito());
            return $regrasDoLancamentoAlteradas;
        }
        return $aRegrasDoLancamento[0];
    }

}
