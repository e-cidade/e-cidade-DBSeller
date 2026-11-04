<?php
/*
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

use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;

require_once(modification("interfaces/IRegraLancamentoContabil.interface.php"));

/**
 * Retorna a regra cadastrada para a apropriação da receita
 * @author Andrio Costa
 * @package contabilidade
 * @subpackage lancamento
 * @version $Revision: 1.8 $
 */
class RegraLancamentoApropriacaoRetencao implements IRegraLancamentoContabil
{
    private $iCodigoDocumento;

    /**
     * A regra para buscar a transação é a seguinte.
     * Primeiro para saber se está configurado corretamente o sistema a transação tem que ter:
     * compara 15 (c47_compara) e todas regras tem que ter o refêrencia (c47_ref) informado.
     * Uma mesma referência pode ter inúmeras regras com elementos diferentes, mas apenas uma regra pode ter conta
     * crédito informado.
     *
     * Sabendo isso a regra tem prioridade de validação.
     * 1º Primeira
     * Se informado o elemento (c114_elemento) na transação, primeiro validamos o elemento.
     * Nesse caso a transação não poder ter conta crédito informada.
     * Se for compatível o elemento com o do empenho, deve-se usar essa regra. A conta crédito vai depender do documento
     * Se não, pula a regra e tenta a próxima
     *
     * 2º Segunda
     * Não estando configurado o elemento, deve-se usar a conta crédito informada na transação.
     *
     * @param int $iCodigoDocumento
     * @param int $iCodigoLancamento
     * @param LancamentoAuxiliarEmpenhoLiquidacao|\ILancamentoAuxiliar $oLancamentoAuxiliar
     *
     * @return bool|\RegraLancamentoContabil
     * @throws \Exception
     */
    public function getRegraLancamento($iCodigoDocumento, $iCodigoLancamento, ILancamentoAuxiliar $oLancamentoAuxiliar)
    {
        $this->iCodigoDocumento = $iCodigoDocumento;
        $iAnoSessao = db_getsession("DB_anousu");
        $oDaoTransacao = new cl_contranslr();
        $sWhere = "     c45_coddoc      = {$iCodigoDocumento}";
        $sWhere .= " and c45_anousu      = {$iAnoSessao}";
        $sWhere .= " and c46_seqtranslan = {$iCodigoLancamento}";
        $sWhere .= " and c47_instit = " . db_getsession('DB_instit');
        $sSqlTransacao = $oDaoTransacao->sql_query(null, "*", "c114_elemento desc", $sWhere);
        $rsTransacao = $oDaoTransacao->sql_record($sSqlTransacao);

        if ($oDaoTransacao->erro_status == '0') {
            return false;
        }

        $iNumeroRegistros = $oDaoTransacao->numrows;

        for ($i = 0; $i < $iNumeroRegistros; $i++) {
            $oDadosTransacao = db_utils::fieldsMemory($rsTransacao, $i);

            if ($iNumeroRegistros == 1 && $oDadosTransacao->c46_ordem > 1) {
                $oDadosTransacao = db_utils::fieldsMemory($rsTransacao, 0);
                $oRegraLancamentoContabil = new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);
                return $oRegraLancamentoContabil;
            }

            switch ($oDadosTransacao->c47_compara) {
                /**
                 * Criado case 0 para ser usado em ordem acima de um onde o reduzido do elemento nao precisa ser igual
                 * ao da conta configurado na regra.
                 */
                case RegraLancamentoContabil::COMPARA_TIPO_RETENCAO:
                    if ($oLancamentoAuxiliar->getTipoCalculoRetencao() != $oDadosTransacao->c47_ref) {
                        break;
                    }

                    if ($oLancamentoAuxiliar->getTipoCalculoRetencao() == $oDadosTransacao->c47_ref
                        && $oDadosTransacao->c46_ordem == 1) {
                        // validando o elemento
                        if (method_exists($oLancamentoAuxiliar, 'getEmpenhoFinanceiro')) {
                            $empenho = $oLancamentoAuxiliar->getEmpenhoFinanceiro();
                            $estruturalEmpenho = new Estrutural($empenho->getContaOrcamento()->getEstrutural());
                            $estruturalValido = !empty($oDadosTransacao->c114_elemento) && $oDadosTransacao->c114_elemento != '000000000000000';

                            if ($estruturalValido) {
                                $estruturalTransacao = new Estrutural($oDadosTransacao->c114_elemento);
                                $nivel = $estruturalTransacao->getNivel();
                                if ($estruturalTransacao->getEstruturalAteNivel() != substr($estruturalEmpenho->getEstrutural(), 0, $nivel)) {
                                    break;
                                }
                            }
                        }

                        $oRegraLancamentoContabil = new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);
                        /**
                         * NO LANÇAMENTO
                         * Se o tipo da retenção é o mesmo da refêrencia da transação (c47_ref) e for o primeiro
                         * lançamento (ordem = 1) e a conta crédito está informada na transação.
                         * Deve-se usar a conta da transação! Isso é válido para todos os documentos de retenção.
                         */
                        $docs = [6000, 6002, 6004, 6006, 6008, 6010, 6012];
                        if (in_array($iCodigoDocumento, $docs) && !empty($oDadosTransacao->c47_credito)) {
                            return $oRegraLancamentoContabil;
                        }
                        /**
                         * NO ESTORNO LANÇAMENTO
                         * Mesma regra acima,só que validando a conta débito
                         */
                        $docs = [6001, 6003, 6005, 6007, 6009, 6011, 6013];
                        if (in_array($iCodigoDocumento, $docs) && !empty($oDadosTransacao->c47_debito)) {
                            return $oRegraLancamentoContabil;
                        }

                        /**
                         * Não atendendo a regra acima, ou seja, não tem a conta informada na transação, devemos
                         * buscar a conta extra orçamentária da retenção.
                         * Isso aplicado aos documentos 6000 e 6001
                         */

                        if (in_array($iCodigoDocumento, [6000, 6001, 6002, 6003, 6004, 6005, 6008, 6009, 6010, 6011])) {
                            $conta = $this->getContaExtraOrcamentariaDaRetencao($oLancamentoAuxiliar->getRetencao());
                            if (in_array($iCodigoDocumento, [6001, 6003, 6005, 6009, 6011])) {
                                $oRegraLancamentoContabil->setContaDebito($conta);
                            } else {
                                $oRegraLancamentoContabil->setContaCredito($conta);
                            }
                        }

                        if (in_array($iCodigoDocumento, array(6012, 6013))) {
                            if (!$oLancamentoAuxiliar->isEstorno()) {
                                $oRegraLancamentoContabil->setContaCredito($oLancamentoAuxiliar->getCodigoReduzido());
                            } else {
                                $oRegraLancamentoContabil->setContaDebito($oLancamentoAuxiliar->getCodigoReduzido());
                            }
                        }

                        if (in_array($iCodigoDocumento, array(6006, 6007))) {
                            if ($iCodigoDocumento == 6006) {
                                $oRegraLancamentoContabil->setContaDebito($oLancamentoAuxiliar->getContaDebito());
                            } else {
                                $oRegraLancamentoContabil->setContaCredito($oLancamentoAuxiliar->getContaCredito());
                            }
                        }
                        return $oRegraLancamentoContabil;
                    }
                    break;
                default :

                    if ($oDadosTransacao->c46_ordem > 1) {
                        $oRegraLancamentoContabil = new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);
                        return $oRegraLancamentoContabil;
                    }
                    break;
            }
        }


        return false;
    }

    /**
     *
     * @param $codigoRetencao
     * @return int
     * @throws \BusinessException
     */
    protected function getContaExtraOrcamentariaDaRetencao($codigoRetencao)
    {
        $anousu = db_getsession("DB_anousu");
        $instit = db_getsession("DB_instit");
        $sSqlTipoRec = " SELECT k02_reduz                                                   ";
        $sSqlTipoRec .= "   from retencaotiporec ";
        $sSqlTipoRec .= "       inner join tabrec  on e21_receita = k02_codigo               ";
        $sSqlTipoRec .= "       inner join tabplan on tabrec.k02_codigo = tabplan.k02_codigo ";
        $sSqlTipoRec .= " where e21_sequencial  = {$codigoRetencao} ";
        $sSqlTipoRec .= "   and k02_anousu = {$anousu}";

        // devido aos RPs poderem ter receita Extra ou Orçamentaria vamos fazer um union com a taborc
        $sSqlTipoRec .= "

        union

        select conplanoreduz.c61_reduz as k02_codigo
           from retencaotiporec
             inner join tabrec on e21_receita = k02_codigo
             inner join taborc on tabrec.k02_codigo = taborc.k02_codigo
             inner join orcreceita on taborc.k02_codrec = orcreceita.o70_codrec
                                  and orcreceita.o70_anousu = taborc.k02_anousu
                                  and orcreceita.o70_instit = {$instit}
             inner join conplanoorcamento on c60_codcon = orcreceita.o70_codfon and c60_anousu=o70_anousu
             inner join conplanoconplanoorcamento  on conplanoconplanoorcamento.c72_conplanoorcamento = conplanoorcamento.c60_codcon
                                                  and conplanoconplanoorcamento.c72_anousu            = conplanoorcamento.c60_anousu
             inner join conplano       on conplano.c60_codcon     = conplanoconplanoorcamento.c72_conplano
                                      and conplano.c60_anousu     = conplanoconplanoorcamento.c72_anousu
             inner join conplanoreduz  on conplano.c60_codcon     = conplanoreduz.c61_codcon
                  where e21_sequencial = {$codigoRetencao}
                    and c61_anousu = {$anousu}
                    and o70_anousu = {$anousu}
                    and c61_instit = {$instit}

        ";

        $rsTipoRec = db_query($sSqlTipoRec);
        if (pg_num_rows($rsTipoRec) == 0) {
            throw new \BusinessException("
                Receita da retenção {$codigoRetencao} sem ligação com Orçamento/Plano de Contas.
                Verifique o cadastro.
            ");
        }
        return db_utils::fieldsMemory($rsTipoRec, 0)->k02_reduz;
    }

    /**
     * Retorna a conta a credito do evento
     * @param $documento
     * @param $ano
     * @param $instituicao
     * @param $tipoRetencao
     * @return bool|int
     */
    public static function getContaCreditoDoDocumento($documento, $ano, $instituicao, $tipoRetencao)
    {

        $dadosConta = self::getContasDoDocumentoComTipodeRetencao($documento, $ano, $instituicao, $tipoRetencao);
        if (!$dadosConta) {
            return false;
        }
        return $dadosConta->credito;
    }

    /**
     * @param $documento
     * @param $ano
     * @param $instituicao
     * @param $tipoRetencao
     * @return bool|int
     */
    public static function getContaDebitoDoDocumento($documento, $ano, $instituicao, $tipoRetencao)
    {

        $dadosConta = self::getContasDoDocumentoComTipodeRetencao($documento, $ano, $instituicao, $tipoRetencao);
        if (!$dadosConta) {
            return false;
        }
        return $dadosConta->debito;
    }

    /**
     * Retorna as contas do 1 lancamento
     * @param $documento
     * @param $ano
     * @param $instituicao
     * @param $tipoRetencao
     * @return _db_fields|bool|stdClass
     */
    protected static function getContasDoDocumentoComTipodeRetencao($documento, $ano, $instituicao, $tipoRetencao)
    {
        $sql = " select c47_debito as debito, ";
        $sql .= "        c47_credito as credito";
        $sql .= "   from contrans ";
        $sql .= "        inner join contranslan on c46_seqtrans = c45_seqtrans ";
        $sql .= "        inner join contabilidade.contranslr on c47_seqtranslan = c46_seqtranslan ";
        $sql .= "  where c45_anousu = {$ano} ";
        $sql .= "    and c45_coddoc = {$documento} ";
        $sql .= "    and c45_instit = {$instituicao}";
        $sql .= "    and c46_ordem = 1 ";
        $sql .= "    and c47_compara = 15";
        $sql .= "    and c47_ref = {$tipoRetencao}";
        $rsRegra = db_query($sql);
        if (pg_num_rows($rsRegra) == 0) {
            return false;
        }
        return \db_utils::fieldsMemory($rsRegra, 0);

    }
}
