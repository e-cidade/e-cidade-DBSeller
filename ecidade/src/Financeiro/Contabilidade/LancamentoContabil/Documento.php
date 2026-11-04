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

namespace ECidade\Financeiro\Contabilidade\LancamentoContabil;

/**
 * Class Documento
 * @package ECidade\Financeiro\Contabilidade\LancamentoContabil
 */
class Documento
{
    /**
     * Estorno de Empenho de Restos a Pagar Não Processados (Não Liquidados)
     * @var integer
     */
    const ESTORNO_RP_NAO_PROCESSADO = 32;

    /**
     * Liquiação de RP
     * @var integer
     */
    const LIQUIDACAO_RP = 33;

    /**
     * Estorno de Liquiação de RP
     * @var integer
     */
    const ESTORNO_LIQUIDACAO_RP = 34;

    /**
     * Liquidação de RP para Estoque e Patrimonio
     * @var integer
     */
    const LIQUIDACAO_RP_ESTOQUE_PATRIMONIO = 39;

    /**
     * Estorno de Liquidação de RP para Estoque e Patrimonio
     * @var integer
     */
    const ESTORNO_LIQUIDACAO_RP_ESTOQUE_PATRIMONIO = 39;

    /**
     * Inscrição de Restos a Pagar Não Processados (Não Liquidados)
     * @var integer
     */
    const INSCRICAO_RP_NAO_PROCESSADO = 1007;

    /**
     * @var integer
     */
    const ENCERRAMENTO_NATUREZA_ORCAMENTARIA_CONTROLE_RECEITA = 1010;

    /**
     * @var integer
     */
    const ENCERRAMENTO_RECEITA_REALIZADA = 1020;

    /**
     * @var integer
     */
    const ENCERRAMENTO_RECEITA_BRUTA = 1021;



    const ENCERRAMENTO_TRANSFERENCIA_SALDOS_RPNP_EX_ANT = 2030;
    const ENCERRAMENTO_TRANSFERENCIA_SALDOS_RPP_EX_ANT = 2031;



    const ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDAR = 1024;
    const ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDACAO = 1025;
    const ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDADOS = 1026;


    const ENCERRAMENTO_RPNP_LIQUIDAR_EXERCICIO = 1011;

    const ENCERRAMENTO_RPNP_LIQUIDACAO_EXERCICIO = 1012;

    /**
     * Lancamentos de inscricao de RP processados do exercicio
     */
    const ENCERRAMENTO_INSCRICAO_RPP_EXERCICIO = 1013;

    /**
     * Lancamentos de inscricao de RPNP processados do exercicio
     */
    const ENCERRAMENTO_INSCRICAO_RPNP_PAGO_EXERCICIO = 1014;

    /**
     * Lancamentos de inscricao de RPNP cancelado do exercicio
     */
    const ENCERRAMENTO_INSCRICAO_RPNP_CANCELADO_EXERCICIO = 1015;

    /**
     * Lancamentos de inscricao de RP pagos exercicio
     */
    const ENCERRAMENTO_INSCRICAO_RPP_PAGOS_EXERCICIO = 1016;

    /**
     * Lancamentos de inscricao de RP cancelados no exercicio
     */
    const ENCERRAMENTO_INSCRICAO_RPP_CANCELADO_EXERCICIO = 1017;

    /**
     * Lancamentos de transferencia de RPNP para RP
     */
    const ENCERRAMENTO_TRANSFERENCIA_RPNP_RP = 1018;

    /**
     * Lancamentos de inscricao de RPNP pagos exercicio
     */
    const ENCERRAMENTO_VARIACOES_PATRIMONIAIS = 1009;

    /**
     * Encerramento das contas de controle da despesa
     */
    const ENCERRAMENTO_NATUREZA_ORCAMENTARIA_CONTROLE_DESPESA = 1019;

    /**
     * Encerramento da Realização de DDR
     */
    const ENCERRAMENTO_DDR_REALIZADA = 1022;

    /**
     * ENCERRAMENTO DE CONTRATOS / CONVÊNIOS EXECUTADOS
     */
    const ENCERRAMENTO_CONTRATOS_CONVENIOS_EXECUTADOS = 1023;

    /**
     * ABERTURA das receitas orcamentárias no exericio
     */
    const ABERTURA_ORCAMENTO_RECEITA = 2003;
    /**
     * abertura das despesas orcamentárias no exericio
     */
    const ABERTURA_ORCAMENTO_DESPESA = 2001;

    /**
     * Registra a transferência do valor advindo de saldos de restos não processados de exercicios anteriores
     * ao imediatamente encerrado.
     * @var int
     */
    const ABERTURA_TRANSFERENCIA_SALDOS_RPNP_EX_ANT = 2030;
    /**
     * Registra a transferência do valor advindo de saldos de restos processados de exercicios anteriores
     * ao imediatamente encerrado.
     * @var int
     */
    const ABERTURA_TRANSFERENCIA_SALDOS_RPP_EX_ANT = 2031;

    /**
     * Registra a transferência do valor advindo de saldos de restos não processados inscritos no exercicio
     * imediatamente anterior encerrado, tanto a liquidar como em liquidação.
     * @var int
     */
    const ABERTURA_TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_EX_ANT = 2032;
    /**
     * Registra a transferência do valor advindo de saldos de restos processados de exercicios
     * anteriores ao imediatamente encerrado.
     * @var int
     */
    const ABERTURA_TRANSFERENCIA_SALDOS_RPP_INSCRITOS_EX_ANT = 2033;


    /**
     * Registra as transferencias para os gerados na 2032
     */
    const TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_LIQUIDAR = 2034;
    const TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_EM_LIQUIDACAO = 2035;
    
    /**
     * Retorna o tipo do documento
     * @param $documento
     * @return int|null
     */
    public static function getTipoDoDocumento($documento)
    {
        $daoConhistDoc = new \cl_conhistdoc();
        $sqlTipoDocumento = $daoConhistDoc->sql_query_file($documento, "c53_tipo");
        $rsTipoDocumento = db_query($sqlTipoDocumento);
        if (!$rsTipoDocumento || pg_num_rows($rsTipoDocumento) == 0) {
            return null;
        }
        return \db_utils::fieldsMemory($rsTipoDocumento, 0)->c53_tipo;
    }


    /**
     * Verifica se o documento é um pagamento extra orçamentário
     * @param $documento
     * @return bool
     */
    public static function isPagamentoExtra($documento)
    {
        $documentosPagamentoExtra = array(161, 163, 151, 153, 120, 121);
        return in_array($documento, $documentosPagamentoExtra);
    }

    /**
     * verifica se o lançamento é um recebimento extra
     * @param $documento
     * @return bool
     */
    public static function isRecebimentoExtra($documento)
    {
        $documentosPagamentoExtra = array(160, 162, 150, 152, 130, 131);
        return in_array($documento, $documentosPagamentoExtra);
    }

    /**
     * Verifica se o documento é uma transferência de decendio
     * @param $documento
     * @return bool
     */
    public static function isTransferenciaDecendio($documento)
    {
        $documentosPagamentoExtra = array(140, 141);
        return in_array($documento, $documentosPagamentoExtra);
    }

    /**
     * Verifica se o documento é uma transferência de cobertura finaceira
     * @param $documento
     * @return bool
     */
    public static function isTransferenciaCoberturaFinanceiro($documento)
    {
        $documentosPagamentoExtra = array(142, 143);
        return in_array($documento, $documentosPagamentoExtra);
    }
}
