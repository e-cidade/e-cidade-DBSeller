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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$rotulocampo = new rotulocampo;
$rotulocampo->label("k11_id");
$rotulocampo->label("k13_conta");

$k00_dtoper = date('Y-m-d', db_getsession("DB_datausu"));
$k00_dtoper_dia = date('d', db_getsession("DB_datausu"));
$k00_dtoper_mes = date('m', db_getsession("DB_datausu"));
$k00_dtoper_ano = date('Y', db_getsession("DB_datausu"));

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
function js_relatorio2() {
  var F = document.form1;
  var datai = F.datai_ano.value+'-'+F.datai_mes.value+'-'+F.datai_dia.value;
  var dataf = F.dataf_ano.value+'-'+F.dataf_mes.value+'-'+F.dataf_dia.value;
  jan = window.open('cai2_extrato002.php?imprime_historico='+F.imprime_historico.value+'&imprime_receitas='+F.imprime_receitas.value+'&totalizador_diario='+F.totalizador_diario.value+'&somente_contas_com_movimento='+F.somente_contas_com_movimento.value+'&datai='+datai+'&dataf='+dataf+'&conta='+document.form1.k13_conta.value+'&imprime_analitico='+document.form1.imprime_analitico.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  jan.moveTo(0,0);
}
</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="if(document.form1) document.form1.elements[0].focus()" >

<div class="container">
  <form name="form1" method="post" action="">
    <fieldset>
      <legend>Extrato Bancário</legend>
       <table>
         <tr>
           <td><strong>Data inicial:</strong></td>
           <td  nowrap>
             <?=db_data("datai", $k00_dtoper_dia, $k00_dtoper_mes, $k00_dtoper_ano)?>
           </td>
         </tr>
         <tr>
           <td><strong>Data final:</strong></td>
           <td  nowrap>
             <?=db_data("dataf", $k00_dtoper_dia, $k00_dtoper_mes, $k00_dtoper_ano)?>
           </td>
         </tr>
         <tr>
           <td title="<?=$Tk13_conta?>"><?=$Lk13_conta?></td>
           <td>
             <?php
                $clsaltes = new cl_saltes;
                $result = $clsaltes->sql_record($clsaltes->sql_query("", "saltes.k13_conta,k13_descr", "k13_descr"));
                db_selectrecord("k13_conta", $result, true, 2, "", "", "", "0");
                ?>
           </td>
         </tr>
        <tr>
          <td><b>Somente contas com movimento </b></td>
           <td>
               <?php
                $matriz = array("n"=>"nao","s"=>"sim");
                $somente_contas_com_movimento = "s";
                db_select("somente_contas_com_movimento", $matriz, true, 1);
                ?>
           </td>
        </tr>
        <tr>
          <td><b>Totalizador diário </b></td>
           <td>
               <?php
                $matriz = array("s"=>"sim","n"=>"nao");
                db_select("totalizador_diario", $matriz, true, 1);
                ?>
           </td>
        </tr>
        <tr>
          <td><b>Imprime receitas </b></td>
           <td>
               <?php
                $matriz = array("s"=>"sim","n"=>"nao");
                db_select("imprime_receitas", $matriz, true, 1);
                ?>
           </td>
        </tr>
        <tr>
          <td><b>Imprime histórico </b></td>
           <td>
               <?php
                $matriz = array("s"=>"sim","n"=>"nao");
                db_select("imprime_historico", $matriz, true, 1);
                ?>
           </td>
        </tr>
        <tr>
          <td><b>Tipo Impressão</b></td>
           <td>
               <?php
                 $matriz = array("a"=>"Analítico","s"=>"Sintético");
                 db_select("imprime_analitico", $matriz, true, 1);
                ?>
           </td>
        </tr>
       </table>
    </fieldset>
    <input name="imprimir" type="button" id="imprimir" onClick="js_relatorio2()" value="Imprimir">
  </form>
</div>
<?php
  db_menu();
?>
</body>
</html>
