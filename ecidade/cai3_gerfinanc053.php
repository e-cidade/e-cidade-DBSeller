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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("libs/db_sql.php"));
include(modification("libs/db_utils.php"));
include(modification("libs/db_app.utils.php"));
include(modification("model/arrecadacao/abatimento/Desconto.model.php"));

$oGet  = db_utils::postMemory($_GET);

?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php

 db_app::load('scripts.js');
 db_app::load('prototype.js');
 db_app::load('estilos.css');

?>
</head>
<body bgcolor=#CCCCCC bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="parent.document.getElementById('processando').style.visibility = 'hidden'">
<center>
<?php

$iInstit = db_getsession('DB_instit');

if (isset($oGet->numcgm)) {
    $sInnerCredito = " inner join arrenumcgm on arrenumcgm.k00_numpre = abatimentorecibo.k127_numprerecibo ";
    $sWhereCredito = " and arrenumcgm.k00_numcgm = ".$oGet->numcgm;
    $sTipoPesquisa = "C";
    $sChavePesquisa= $oGet->numcgm;

    $sInnerCompensacao = " inner join arrenumcgm on arrenumcgm.k00_numpre =  abatimentoutilizacaodestino.k170_numpre ";
    $sInnerDevolucao   = " inner join arrenumcgm on arrenumcgm.k00_numpre = arreckey.k00_numpre ";
    $sWhereCompensacao = $sWhereCredito;
    $sWhereDevolucao   = $sWhereCompensacao;
} elseif (isset($oGet->matric)) {
    $sInnerCredito = " inner join arrematric on arrematric.k00_numpre = abatimentorecibo.k127_numprerecibo ";
    $sWhereCredito = " and arrematric.k00_matric = " . $oGet->matric;
    $sTipoPesquisa = "M";
    $sChavePesquisa= $oGet->matric;

    $sInnerCompensacao = " inner join arrematric on arrematric.k00_numpre = abatimentoutilizacaodestino.k170_numpre ";
    $sInnerDevolucao   = " inner join arrematric on arrematric.k00_numpre = arreckey.k00_numpre ";
    $sWhereCompensacao = $sWhereCredito;
    $sWhereDevolucao   = $sWhereCompensacao;
} elseif (isset($oGet->inscr)) {
    $sInnerCredito = " inner join arreinscr on arreinscr.k00_numpre = abatimentorecibo.k127_numprerecibo ";
    $sWhereCredito = " and arreinscr.k00_inscr = " . $oGet->inscr;
    $sTipoPesquisa = "I";
    $sChavePesquisa= $oGet->inscr;

    $sInnerCompensacao = " inner join arreinscr on arreinscr.k00_numpre = abatimentoutilizacaodestino.k170_numpre ";
    $sInnerDevolucao   = " inner join arreinscr on arreinscr.k00_numpre = arreckey.k00_numpre ";
    $sWhereCompensacao = $sWhereCredito;
    $sWhereDevolucao   = $sWhereCompensacao;
} else {
    $sInnerCredito = "";
    $sWhereCredito = " and abatimentorecibo.k127_numprerecibo = " . $oGet->numpre;

    $sInnerCompensacao = "";
    $sWhereCompensacao = " abatimentoutilizacaodestino.k170_numpre = " . $oGet->numpre;

    $sInnerDevolucao = "";
    $sWhereDevolucao = " arreckey.k00_numpre = " . $oGet->numpre;
}

  /**
   * BUSCA OS DESCONTOS CONCEDIDOS
   * ADICIONANDO A VARIAVEL $aDadosSaida[]
   */
  // TODO: Verificar se deve aparecer no relatório das compensações utilizadas
  $aDescontos = !isset($sChavePesquisa) ?
                array() :
                Desconto::getDescontosPorOrigem($sTipoPesquisa, $sChavePesquisa);

foreach ($aDescontos as $oDescontos) {
    $oDados                  = new stdClass();
    $oDados->k01_codigo      = $oDescontos->getTipoAbatimento();
    $oDados->k125_sequencial = $oDescontos->getCodigo();
    $oDados->k00_tipo        = $oDescontos->getTipoDebito();
    $oDados->k00_descr       = !empty($oDados->k00_tipo) ? getDescricaoTipoDebito($oDescontos->getTipoDebito()) : "";
    $oDados->k125_datalanc   = $oDescontos->getDataLancamento()->getDate();
    $oDados->k00_hist        = Desconto::HISTORICO;
    $oDados->k01_descr       = 'DESCONTO';
    $oDados->k170_numpre     = $oDescontos->getNumpre();
    $oDados->k170_numpar     = $oDescontos->getNumpar();
    $oDados->k02_descr       = $oDescontos->getDescRec();

    if ($oDescontos->getSituacao() == Abatimento::SITUACAO_CANCELADO) {
        $oDados->k00_hist      = Desconto::HISTORICO_CANCELAMENTO;
        $oDados->k01_descr     = 'DESCONTO CANCELADO';
    }

    $oDados->k00_valor       = $oDescontos->getValor();
    $oDados->sTipo           = 'desconto';
    $aDadosSaida[]           = $oDados;
}


if (count($aDadosSaida) > 0) {
    ?>
    <table border="1" cellspacing="0" cellpadding="3">
      <tr bgcolor="#FFCC66">
        <th nowrap>MI                  </th>
        <th nowrap>Cód. Abatimento     </th>
        <th nowrap>Numpre              </th>
        <th nowrap>Tipo de Movimento   </th>
        <th nowrap>Receita             </th>
        <th nowrap>Valor               </th>
        <th nowrap>Data                </th>
        <th nowrap>Observação          </th>
      </tr>
    <?php

    $sCor1   = "#EFE029";
    $sCor2   = "#E4F471";
    $sCorRow = $sCor1;
}
foreach ($aDadosSaida as $oCredito) {
    if ($sCorRow == $sCor1) {
        $sCorRow = $sCor2;
    } else {
        $sCorRow = $sCor1;
    }
    ?>
      <tr bgcolor="<?=$sCorRow?>">
        <td align="center" nowrap >
      <?php
        /**
         * Verifica se deve exibir as informações do desconto ou da compensação
         */
        if ($oCredito->sTipo == 'desconto') {
            db_ancora('MI', "js_consultaDesconto({$oCredito->k125_sequencial})", 1, '');
        } else {
            db_ancora('MI', "js_consultaOrigemCredito({$oCredito->k125_sequencial},
              {$oCredito->abatimentoorigem})", 1, '');
        }

        ?>
        </td>
        <td align="center" nowrap ><?=$oCredito->k125_sequencial ?>&nbsp;</td>
        <td align="center" nowrap ><?=$oCredito->k170_numpre ?>&nbsp;</td>
        <td align="center" nowrap ><?=$oCredito->k01_descr ?>&nbsp;</td>
        <td align="center" nowrap ><?= (isset($oCredito->k00_receit) ? $oCredito->k00_receit : ' - ')?></td>
        <td align="right"  nowrap ><?=db_formatar($oCredito->k00_valor, 'f')?>&nbsp;</td>
        <td align="center" nowrap ><?=db_formatar($oCredito->k125_datalanc, 'd')?>&nbsp;</td>
        <td align="left" nowrap ><?=(isset($oCredito->k157_observacao) ? $oCredito->k157_observacao  : '')?>&nbsp;</td>
      </tr>
    <?php
}

?>
</table>
</center>
</body>
</html>
<script type="text/javascript">

  function js_consultaOrigemCredito(iAbatimento, abatimentoOrigem) {

    var sUrl = 'func_compensacao.php?iAbatimento='+iAbatimento+'&abatimentoOrigem='+abatimentoOrigem;
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_compensacao',sUrl,'Origem da Compensação',true);

  }

  function js_consultaDesconto(iAbatimento) {

    var sUrl = 'func_compensacaodesconto.php?iAbatimento='+iAbatimento;
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_desconto',sUrl,'Origem do Desconto',true);
  }


</script>
<?php
/**
 *
 */
function getDescricaoTipoDebito($iTipoDebito)
{
    static $aTiposDebito;

    if (empty($aTiposDebito[$iTipoDebito])) {
        $oDaoArretipo = new cl_arretipo();
        $sSql         = $oDaoArretipo->sql_query_file($iTipoDebito);
        $rsSql        = db_query($sSql);

        if (!$rsSql || pg_num_rows($rsSql) == 0) {
            $aTiposDebito[$iTipoDebito] = '';
            return $aTiposDebito[$iTipoDebito];
        }
        $aTiposDebito[$iTipoDebito] = db_utils::fieldsMemory($rsSql, 0)->k00_descr;
    }
    return $aTiposDebito[$iTipoDebito];
}
