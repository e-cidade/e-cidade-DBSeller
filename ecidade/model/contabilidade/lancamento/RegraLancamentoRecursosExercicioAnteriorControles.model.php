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

require_once(modification('interfaces/IRegraLancamentoContabil.interface.php'));

/**
 * Class RegraLancamentoTef
 */
class RegraLancamentoRecursosExercicioAnteriorControles implements IRegraLancamentoContabil
{
    /**
     * Deve retornar qual uma instancia da RegraLancamento contendo as contas para efetuar o lançamento
     *
     * @param integer $iCodigoDocumento
     * @param integer $iCodigoLancamento
     * @param ILancamentoAuxiliar $oLancamentoAuxiliar
     *
     * @return RegraLancamentoContabil
     * @throws Exception
     */
    public function getRegraLancamento($iCodigoDocumento, $iCodigoLancamento, ILancamentoAuxiliar $oLancamentoAuxiliar)
    {
        $lEstorno = $oLancamentoAuxiliar->isEstorno();
        $oEventoContabil = EventoContabilRepository::getEventoContabilByCodigo(
            $iCodigoDocumento,
            db_getsession('DB_anousu'),
            db_getsession('DB_instit')
        );
        $oLancamentos = $oEventoContabil->getEventoContabilLancamentoPorCodigo($iCodigoLancamento);

        if (!$oLancamentos || count($oLancamentos->getRegrasLancamento()) == 0) {
            return false;
        }

        if (count($oLancamentos->getRegrasLancamento()) > 1 ) {

            $mensagem = "Mais de uma regra cadastrada para o documento {$iCodigoDocumento} - ";
            $mensagem .= "{$oEventoContabil->getDescricaoDocumento()} de ordem {$oLancamentos->getOrdem()}.";
            throw new Exception($mensagem);
        }

        $oRegra = $oLancamentos->getRegrasLancamento();
        $regra = $oRegra[0];

        $iContaCredito = $regra->getContaCredito();
        $iContaDebito = $regra->getContaDebito();

        if ($lEstorno) {

            $iContaCredito = $regra->getContaDebito();
            $iContaDebito = $regra->getContaCredito();
        }
        $oRegraLancamentoContabil = new RegraLancamentoContabil();
        $oRegraLancamentoContabil->setContaCredito($iContaCredito);
        $oRegraLancamentoContabil->setContaDebito($iContaDebito);
        return $oRegraLancamentoContabil;

    }
}
