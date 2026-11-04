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
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_rhregime_classe.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$clrotulo = new rotulocampo;
$clrotulo->label("rh37_funcao");
$clrotulo->label("rh37_descr");
$clrotulo->label("DBtxt23");
$clrotulo->label("DBtxt25");
$clrhregime = new cl_rhregime;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="if(document.form1.rh37_funcao)document.form1.rh37_funcao.focus();">
<br><br><br>
<div align="center">
<form name="form1" method="post">

<table>
  <tr>
  <td>
  <fieldset>
  <legend><b>Consultas de cargos</b></legend>

	  <table border="0">

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td align="right" nowrap title="Digite o Ano / Mes de competência" >
            <strong>Ano / Mês :</strong>
          </td>
          <td>
            <?php
            $ano = db_anofolha();
            db_input('ano',4,$IDBtxt23,true,'text',2,'')
            ?>
            &nbsp;/&nbsp;
            <?php
            $mes = db_mesfolha();
            db_input('mes',2,$IDBtxt25,true,'text',2,'')
            ?>
          </td>
        </tr>
        <tr>
          <td align="right" title="<?php echo $Trh37_funcao?>">
            <?php
            db_ancora(@ $Lrh37_funcao, "js_pesquisarfuncao(true);", 1);
    		?>
          </td>
          <td>
            <?php
            db_input('rh37_funcao', 8, $Irh37_funcao, true, 'text', 1, " onchange='js_pesquisarfuncao(false);'")
            ?>
            <?php
            db_input('rh37_descr', 30, $Irh37_descr, true, 'text', 3, '');
            ?>
          </td>
        </tr>

        <tr>
          <td align="right" title="Lotação">
            <?php
            db_ancora("Lotação:", "js_pesquisarlotacao(true);", 1);
    		    ?>
          </td>
          <td>
            <?php
            db_input('r70_codigo', 8, 3, true, 'text', 1, " onchange='js_pesquisarlotacao(false);'")
            ?>
            <?php
            db_input('r70_descr', 30, 3, true, 'text', 3, '');
            ?>
          </td>
        </tr>

        <tr>
          <td align="right" title="Seleção">
            <?php
                db_ancora("Seleção:", "js_pesquisarselecao(true);", 1);
    		    ?>
          </td>
          <td>
            <?php
                db_input('r44_selec', 8, 3, true, 'text', 1, " onchange='js_pesquisarselecao(false);'")
            ?>
            <?php
                db_input('r44_descr', 30, 3, true, 'text', 3, '');
            ?>
          </td>
        </tr>

        <tr>
          <td colspan="2" >
                  <tr>
                    <td align="center" colspan="2">
                      <?php
                      $result_regime = $clrhregime->sql_record($clrhregime->sql_query_file(null, "rh30_codreg, rh30_codreg||'-'||rh30_descr as rh30_descr", "rh30_descr" , " rh30_instit = ".db_getsession('DB_instit') ));
                      db_multiploselect("rh30_codreg", "rh30_descr", "nselecionados", "sselecionados", $result_regime, array(), 5, 250);
                      ?>
                    </td>
                  </tr>
          </td>
        </tr>

      </table>


  </fieldset>
  </td>
  </tr>

</table>

<table>
  <tr>
    <td height="25" colspan="2" align="center">
      <input type="button" value="Consultar" name="pesquisar" onclick="js_abrejan();">
    </td>
  </tr>
</table>

</form>
<?php
 db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>

</div>
</body>
</html>
<script>
function js_abrejan(){
  qry = "";
  rog = "?";
  if(document.form1.rh37_funcao.value!=""){
    qry = rog+"funcao="+document.form1.rh37_funcao.value;
    rog = "&";
  }
  if(document.form1.mes.value!=""){
    qry += rog+"mes="+document.form1.mes.value;
    rog = "&";
  }
  if(document.form1.ano.value!=""){
    qry += rog+"ano="+document.form1.ano.value;

  }
  if(document.form1.r70_codigo.value!=""){
    qry += rog+"lotacao="+document.form1.r70_codigo.value;

  }
  if(document.form1.r44_selec.value!=""){
    qry += rog+"selecao="+document.form1.r44_selec.value;

  }

  selecionados = "";
  virgula_ssel = "";
  for(var i=0; i<document.form1.sselecionados.length; i++){
    selecionados+= virgula_ssel + document.form1.sselecionados.options[i].value;
    virgula_ssel = ",";
  }

  if (selecionados == "") {
    alert('Selecione ao menos um regime para impressão do relatório');
    return false;
  }
  qry += "&colunas1="+selecionados;
  location.href = 'pes3_consrhfuncao002.php'+qry;
}
function js_pesquisarfuncao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhfuncao','func_rhfuncao.php?funcao_js=parent.js_mostrafuncao1|rh37_funcao|rh37_descr','Pesquisa',true);
  }else{
     if(document.form1.rh37_funcao.value != ''){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhfuncao','func_rhfuncao.php?pesquisa_chave='+document.form1.rh37_funcao.value+'&funcao_js=parent.js_mostrafuncao','Pesquisa',false);
     }else{
       document.form1.rh37_descr.value = '';
     }
  }
}
function js_mostrafuncao(chave,erro){
  document.form1.rh37_descr.value  = chave;
  if(erro==true){
    document.form1.rh37_funcao.value = '';
    document.form1.rh37_funcao.focus();
  }
}
function js_mostrafuncao1(chave1,chave2){
  document.form1.rh37_funcao.value  = chave1;
  document.form1.rh37_descr.value  = chave2;
  db_iframe_rhfuncao.hide();
}



function js_pesquisarselecao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao','func_selecao.php?funcao_js=parent.js_mostraselecao1|r44_selec|r44_descr','Pesquisa',true);
  }else{
     if(document.form1.r44_selec.value != ''){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao','func_selecao.php?pesquisa_chave='+document.form1.r44_selec.value+'&funcao_js=parent.js_mostraselecao','Pesquisa',false);
     }else{
       document.form1.r44_descr.value = '';
     }
  }
}

function js_mostraselecao(chave,erro){
  document.form1.r44_descr.value  = chave;
  if(erro==true){
    document.form1.r44_descr.value = '';
    document.form1.r44_descr.focus();
  }
}

function js_mostraselecao1(chave1,chave2){
  document.form1.r44_selec.value  = chave1;
  document.form1.r44_descr.value  = chave2;
  db_iframe_selecao.hide();
}


function js_pesquisarlotacao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhlota','func_rhlota.php?funcao_js=parent.js_mostralotacao1|r70_codigo|r70_descr','Pesquisa',true);
  }else{
     if(document.form1.r70_codigo.value != ''){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhlota','func_rhlota.php?pesquisa_chave='+document.form1.r70_codigo.value+'&funcao_js=parent.js_mostralotacao','Pesquisa',false);
     }else{
       document.form1.r70_descr.value = '';
     }
  }
}

function js_mostralotacao(chave,erro){
  document.form1.r70_descr.value  = chave;
  if(erro==true){
    document.form1.r70_descr.value = '';
    document.form1.r70_descr.focus();
  }
}

function js_mostralotacao1(chave1,chave2){
  document.form1.r70_codigo.value  = chave1;
  document.form1.r70_descr.value  = chave2;
  db_iframe_rhlota.hide();
}

</script>
