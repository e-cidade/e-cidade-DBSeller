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
include(modification("libs/db_usuariosonline.php"));
include(modification("libs/db_libpessoal.php"));
include(modification("classes/db_selecao_classe.php"));
include(modification("dbforms/db_funcoes.php"));

$clselecao  = new cl_selecao;
$clselecao->rotulo->label();

$db_opcao   = 1;
$db_botao   = true;

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">

<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/strings.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>

<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >

<br><br><br>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
 <tr> 
  <td align="center" bgcolor="#CCCCCC">
   <form name="form1">
       
    <fieldset style="width: 550px;">
      <legend> <b> Exclusão de férias em Lote</b> </legend>
        <table>
          <tr>
            <td title="<?=@$Tr44_selec?>">
              <?php db_ancora(@$Lr44_selec, "js_pesquisar_selecao(true);", $db_opcao); ?>
            </td>
            <td>
              <?php 
                 db_input('selecao', 8, "", true, 'text', $db_opcao, " onchange='js_pesquisar_selecao(false);'");
                 db_input('r44_descr', 50, $Ir44_descr, true, 'text', 3);
              ?>
            </td>
          </tr>
        </table>
        
    </fieldset>
    
    <input name="excluir" value="Excluir férias" type="button" onblur="document.form1.selecao.focus();" onclick="return js_excluirFerias();">
    
   </form>
  </td>
 </tr>
</table>

<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>

<script>
var sUrlRPC = 'pes4_cadastroferias.RPC.php';  
var oParam  = new Object();

js_tabulacaoforms("form1","selecao",true,1,"selecao",true);

function js_verificaCampos(){
  
  if(document.form1.selecao.value == ""){
    alert("Seleção não informada. Verifique!");
    document.form1.selecao.focus();
    return false;
  }

  if (confirm("Confirma exclusão das férias?")) {
    return true;
  } else {
    return false;
  }  
  
}

function js_excluirFerias() {

  js_verificaCampos();

  oParam.sExec          = 'excluirFeriasLote';
  oParam.iCodigoSelecao = $F("selecao");

  js_divCarregando("Processando exclusão de Férias em Lote...",'msgBox');

  var oAjaxLista  = new Ajax.Request(sUrlRPC,
          {method     :  "post",
           parameters : 'json=' + Object.toJSON(oParam),
           onComplete :  js_retornoExcluirFeriasLote
          }); 
  
}

function js_retornoExcluirFeriasLote(oAjax) {

  js_removeObj('msgBox');
  var oRetorno = JSON.parse(oAjax.responseText);
  alert(oRetorno.sMessage.urlDecode());
  
}


function js_pesquisar_selecao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao','func_selecao.php?funcao_js=parent.js_mostraselecao1|r44_selec|r44_descr','Pesquisa',true);
  }else{
    if(document.form1.selecao.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao','func_selecao.php?pesquisa_chave='+document.form1.selecao.value+'&funcao_js=parent.js_mostraselecao','Pesquisa',false);
    }else{
      document.form1.r44_descr.value = '';
    }
  }
}

function js_mostraselecao(chave,erro){
  document.form1.r44_descr.value = chave;
  if(erro == true){
    document.form1.selecao.focus(); 
    document.form1.selecao.value = '';
  }
}

function js_mostraselecao1(chave1,chave2){
  document.form1.selecao.value = chave1;
  document.form1.r44_descr.value = chave2;
  db_iframe_selecao.hide();
}
</script>