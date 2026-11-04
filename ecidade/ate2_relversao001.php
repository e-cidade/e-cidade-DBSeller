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
include(modification("dbforms/db_classesgenericas.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_db_versao_classe.php"));

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

$clrotulo = new rotulocampo;
$clrotulo->label("nomeinst");
$clrotulo->label("anousu");
$clrotulo->label("codigo");
$clrotulo->label("login");
$clrotulo->label("id_item");
$clrotulo->label("nome_modulo");
$db_opcao = 1;
$db_botao = true;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_relatorio(){
  form = document.form1;

  js_seleciona_combo(form.clientessel);

  imprimir = true;
  if(form.clientessel.length == 0 && form.at01_codcli.value == ""){
    if(!confirm("Gerar relatório pata todos os clientes?")){
      imprimir = false;
    }
  }
  if(imprimir == true){
  
    cliente = "";
    virg = "";
    for(var i=0; i<document.form1.clientessel.length; i++){
      cliente += virg+document.form1.clientessel.options[i].value;
      virg = ",";
    }
    js_OpenJanelaIframe('CurrentWindow.corpo','db_clientes_imprime','con3_versao005.php?versao_inicial='+document.form1.versao_inicial.value+'&dirpadrao='+document.form1.dirpadrao.value+'&tipo_relatorio='+document.form1.tipo_relatorio.value+'&cliente='+cliente,'Gerando Arquivo',false);
  
    //jan = window.open("","db_clientes_imprime","width="+(screen.availWidth-5)+",height="+(screen.availHeight-40)+",scrollbars=0,location=0 ");
    //document.form1.action = "con3_versao005.php";
    //document.form1.target = "db_usuarios_imprime";
    //setTimeout("document.form1.submit()",1000);
  }

}
</script>

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
      <form name="form1" method="post" action="">
      <center>
      <table border="0">
	<tr>
	  <td colspan="4">
	    <table>
	    <?php
	    $aux = new cl_arquivo_auxiliar;
	    $aux->cabecalho  = "<strong>Seleciona Clientes</strong>";
	    $aux->codigo     = "at01_codcli";
	    $aux->descr      = "at01_nomecli";
	    $aux->nomeobjeto = "clientessel";
	    $aux->funcao_js  = 'js_mostradb_clientes';
	    $aux->funcao_js_hide = 'js_mostraclientes1';
	    $aux->func_arquivo = "func_clientes.php";
	    $aux->nomeiframe = "db_iframe_clientes";
	    $aux->executa_script_apos_incluir = "document.form1.at01_codcli.focus();";
	    $aux->executa_script_lost_focus_campo = "js_insSelectclientessel();";
	    $aux->executa_script_change_focus = "document.form1.at01_codcli.focus();";
	    $aux->mostrar_botao_lancar = false;
	    $aux->db_opcao = 2;
	    $aux->tipo = 2;
	    $aux->top = 20;
	    $aux->linhas = 5;
	    $aux->vwidth = "420";
	    $aux->tamanho_campo_descricao = 40;
	    $aux->ordenar_itens = true;
	    $aux->funcao_gera_formulario();
	    ?>
	    </table>
	  </td>
	</tr>

	<tr>
	  <td align='right'><strong>Versão Inicial:</strong>
	  </td>
    <td colspan="3">
	  <select name='versao_inicial' >
    <?php
    $cldb_versao = new cl_db_versao;
    $result = $cldb_versao->sql_record($cldb_versao->sql_query_file(null,"db30_codver,db30_codversao,db30_codrelease","db30_codver desc"));
    if( $cldb_versao->numrows > 0 ){
      for($i=0;$i<$cldb_versao->numrows;$i++){
	      db_fieldsmemory($result,$i);
        echo "<option value='$db30_codver'>2.$db30_codversao.$db30_codrelease</option>";
	    }
    }
    ?>
    </select>
	  </td>
	</tr>

	<tr>
	  <td  align='right'><strong>Tipo de Relatório:</strong>
	  </td>
    <td colspan="3">
	  <select name='tipo_relatorio' >
	   <option value='1'>Geral</option>
	   <option value='2'>Por Cliente</option>
	  </select>
	  </td>
	</tr>
	<tr>
	  <td  align='right'><strong>Diretório Padrão:</strong>
	  </td>
    <td colspan="3">
        <input name="dirpadrao" type="text" value="tmp" width="50">
	  </td>
	</tr>
	</table>
        <input name="relatorio" type="button" value="Relatório" onClick="js_relatorio();">
      </center>
      </form>
    </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>