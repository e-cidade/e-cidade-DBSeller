<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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


$clorcorgao = new cl_orcorgao;
$clorcunidade = new cl_orcunidade;
$clconlancamdoc = new cl_conlancamdoc;
$clrotulo = new rotulocampo;
$clrotulo->label("o40_orgao");
$clrotulo->label("o41_unidade");
$clconlancamdoc->rotulo->label();

db_postmemory($_POST);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
function js_abre(botao){
  dataINI = "";
  dataFIM = "";
  if(document.form1.t71_dataINI_dia.value!="" && document.form1.t71_dataINI_mes.value!="" && document.form1.t71_dataINI_ano.value!=""){
    dataINI = document.form1.t71_dataINI_ano.value+"-"+document.form1.t71_dataINI_mes.value+"-"+document.form1.t71_dataINI_dia.value;
  }
  if(document.form1.t71_dataFIM_dia.value!="" && document.form1.t71_dataFIM_mes.value!="" && document.form1.t71_dataFIM_ano.value!=""){
    dataFIM = document.form1.t71_dataFIM_ano.value+"-"+document.form1.t71_dataFIM_mes.value+"-"+document.form1.t71_dataFIM_dia.value;
  }

  obj  = document.form1.db_processop;
  descr = "";
  for(i=0; i<obj.options.length; i++){
    if(obj.options[i].selected == true){
      descr = obj.options[i].text;
    }
  }

  if(dataINI=="" && dataFIM=="" && document.form1.db_processop.value==0){
    alert("Sem informações para gerar relatório.");
  }else{
    jan = window.open('cai2_processemp002.php?tipo='+document.form1.tipo.value+'&orgao='+document.form1.o40_orgao.value+'&unidade='+document.form1.o41_unidade.value+'&datai='+dataINI+'&dataf='+dataFIM+'&db_processop='+document.form1.db_processop.value+'&db_processdescr='+descr+'&db_ordemop='+document.form1.db_ordemop.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }
}
</script>  
</head>
<body>
    <div class="container">
        <form name="form1" method="post">
            <fieldset>
                <legend>Processos do Empenho </legend>
                <table>
                  <tr> 
                    <td nowrap title="<?=$Tc71_data?>"><b>Período de </b></td>
                    <td nowrap colspan="3">
                    <?php
                      db_inputdata('t71_data', @$t71_data_dia, @$t71_data_mes, @$t71_dats_ano, true, 'text', 1, '', "t71_dataINI");
                    ?>
                    <b>a</b>
                    <?php
                      db_inputdata('t71_data', @$t71_data_dia, @$t71_data_mes, @$t71_dats_ano, true, 'text', 1, '', "t71_dataFIM");
                    ?>
                    </td>
                  </tr>
                  <tr> 
                    <td  nowrap title="Escolha os processos a serem impressos"> <b>Opção: </b>  </td>
                    <td nowrap>
                      <?php
                        $processo = array("0"=>"Selecione uma opção",
                                        "10,11"=>"Empenhado - Estornado",
                                        "20,21"=>"Liquidado - Estornado",
                                        "30,31"=>"Pago - Estornado");
                        db_select('db_processop', $processo, true, 1);
                        ?>
                    </td>
                    <td  nowrap title="Ordem dos campos na impressão."> <b>Ordem: </b>  </td>
                    <td nowrap>
                      <?php
                        $ordem = array("c71_data"=>"Data",
                                     "e60_codemp::text::int"=>"Empenho",
                                     "z01_nome"=>"Nome");
                        db_select('db_ordemop', $ordem, true, 1);
                        ?>
                    </td>
                  </tr>
                  <tr>
                    <td  nowrap > <b>Tipo: </b>  </td>
                    <td nowrap>
                      <?php
                        $xtipo = array("e"=>"Empenho",
                                     "r"=>"RP");
                        db_select('tipo', $xtipo, true, 1);
                        ?>
                    </td>
                  </tr>
                  <tr>
                  <td><?=$Lo40_orgao?></td>
                  <td>
                  <?php
                    $result = $clorcorgao->sql_record($clorcorgao->sql_query(null, null, "o40_orgao,o40_descr", "o40_orgao", "o40_anousu=".db_getsession("DB_anousu")." and o40_instit=".db_getsession("DB_instit")));
                    db_selectrecord("o40_orgao", $result, true, 2, "", "", "", "0", "document.form1.submit();");
                    ?>
                  </td>
                  </tr>
                  <tr>
                  <td><?=$Lo41_unidade?></td>
                  <td>
                  <?php
                    if (isset($o40_orgao) && $o40_orgao != '0') {
                        $result = $clorcunidade->sql_record($clorcunidade->sql_query(null, null, null, "o41_unidade,o41_descr", "o41_unidade", "o41_anousu=".db_getsession("DB_anousu")."  and o41_orgao=$o40_orgao "));
                        db_selectrecord("o41_unidade", $result, true, 2, "", "", "", ($clorcunidade->numrows>1?"0":""));
                    } else {
                        db_input("o41_unidade", 6, 0, true, "hidden", 0);
                    }
                    ?>
                  </td>
                    </tr>
                  <tr>
                  </table>
            </fieldset>
            <input name="relatorio" type="button" onclick='js_abre();'  value="Gerar relatório">
        </form>
    </div>
<?php
db_menu();
?>
</body>
</html>