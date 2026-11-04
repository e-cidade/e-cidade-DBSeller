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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_contabancaria_classe.php"));
include(modification("dbforms/db_funcoes.php"));

include(modification("classes/db_conciliapendcorrente_classe.php"));
include(modification("classes/db_conciliapendextrato_classe.php"));
include(modification("classes/db_conciliacor_classe.php"));
include(modification("classes/db_conciliaextrato_classe.php"));
include(modification("classes/db_conciliaitem_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clcontabancaria = new cl_contabancaria;
$db_opcao = 1;
$db_botao = false;

$oGet = db_utils::postMemory($_GET);

//MODULO: Configuracoes
$clcontabancaria->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("db89_codagencia");

$errosql = false;

if( isset($db83_sequencial) ) {

   $reduz = $db83_sequencial;

   $sWhere = " c56_reduz = ".$db83_sequencial;

   $sSql = $clcontabancaria->sql_query_concilia(null,"db83_sequencial, db83_descricao, k68_data as data"," k68_data desc limit 1",$sWhere);
   $result = $clcontabancaria->sql_record($sSql);

   if($clcontabancaria->numrows!=0){
      db_fieldsmemory($result,0);

      $data_dia = substr($data,8,2);
      $data_mes = substr($data,5,2);
      $data_ano = substr($data,0,4);


      $errosql = false;
   }else{
      $errosql = true;
      $mensagem = "Conta Bancáaria não Encontrada!";
   }

}



?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<form name="form1" >
<table align="center" style="padding:45px;">
  <tr>
    <td>
    <center>
			<center>
			<fieldset>
			  <legend>
			    <b>Cadastro de Conta Bancária</b>
			  </legend>
			  <table border="0">
			    <tr>
			      <td nowrap title="<?=@$Tdb83_descricao?>">
			        <?=db_ancora(@$Ldb83_sequencial,"js_pesquisadb83_sequencial(true);",1);?>
			      </td>
			      <td>
			        <?php
			          db_input('db83_sequencial',10,$Idb83_sequencial,true,'text',3,"");
			          db_input('db83_descricao',50,$Idb83_descricao,true,'text',3,"");
			          db_input('reduz',20,'',true,'hidden',3,"");
			        ?>
			      </td>
			    </tr>
          <tr>
            <td nowrap title="<?=@$Tdb83_bancoagencia?>">
              <b>Data de processamento : </b>
            </td>
            <td>
              <?php

                db_inputdata('data',$data_dia,$data_mes,$data_ano,true,'text',3);
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="Observações">
              <b>Observações da exclusão : </b>
            </td>
            <td>
              <?php
                db_textarea("obs",5,80,null,true,"",1,"");
              ?>
            </td>
          </tr>

			  </table>
			</fieldset>
			</center>
			<input name="processar" type="button" id="processar" value="Processar" onclick="return js_prescreve();" >

    </center>
  </td>
  </tr>
</table>
</form>

<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>

<script>
function js_prescreve() {

   if ($F('data') == '') {
     alert("Informe a data de processamento ! ");
     return false;
   }

   if ($F('db83_sequencial') == '') {
     alert("Informe a conta para processamento ! ");
     return false;
   }

   if ($F('obs') == '') {
     alert("Informe a Observação ! ");
     return false;
   }

  var oParam             = new Object();
  oParam.exec            = 'exclusao';
  oParam.data            = $F('data');
  oParam.db83_sequencial = $F('db83_sequencial');
  oParam.obs             = $F('obs');

  var msgDiv = "Aguarde ...";
  js_divCarregando(msgDiv,'msgBox');

  var oAjax              = new Ajax.Request('cai1_conciliazeralog.RPC.php',
                                             {method: "post",
                                              parameters:'json='+Object.toJSON(oParam),
                                              onComplete: js_retorno
                                             });

}

function js_retorno(oAjax){

  js_removeObj('msgBox');

  var oRetorno = JSON.parse(oAjax.responseText);

  if (oRetorno.status == 1) {
	  alert("Exclusão efetuada com sucesso!");
	  location.href='cai1_conciliazeralog001_manual.php?db83_sequencial=<?=(isset($db83_sequencial)?$reduz:0)?>';
  } else {
	  alert(oRetorno.message.urlDecode());
  }

}


function js_pesquisadb83_sequencial(mostra){

  if(mostra==true){
    var sUrl = 'func_concilia_manual.php?funcao_js=parent.js_mostrasequencial1|dl_reduzido|k13_descr'
  //  var sUrl = 'func_contabancariaconcilia.php?funcao_js=parent.js_mostrasequencial1|db83_sequencial|db83_descricao'
  }else{
    var sUrl = 'func_contabancariaconcilia.php?pesquisa_chave='+$F('db83_sequencial')+'&funcao_js=parent.js_mostrasequencial|db83_sequencial|db83_descricao';
  }
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_contabancaria',sUrl,'Pesquisa',mostra);

}

function js_mostrasequencial(chave1,chave2){
  $('db83_sequencial').value = chave1;
  $('db83_descricao').value  = chave2;

  document.form1.submit();
}

function js_mostrasequencial1(chave1,chave2){

  $('db83_sequencial').value = chave1;
  $('db83_descricao').value  = chave2;
  db_iframe_contabancaria.hide();

  document.form1.submit();

}

function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_contabancaria','func_contabancaria.php?funcao_js=parent.js_preenchepesquisa|dl_reduzido','Pesquisa',true);

}
function js_preenchepesquisa(chave1,chave2){
  $F('db83_sequencial') = chave1;
  $F('db83_descricao')  = chave2;
  db_iframe_contabancaria.hide();
}

<?php
if( ! isset($db83_sequencial) ) {

   echo "js_pesquisadb83_sequencial(true);";

}

if( $errosql == true ){
  echo "alert('$mensagem');";
}
?>
</script>
