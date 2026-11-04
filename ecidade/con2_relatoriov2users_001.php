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
require_once(modification("classes/db_db_usuarios_classe.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

$cldb_usuarios = new cl_db_usuarios;
$cldb_usuarios->rotulo->label();

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
                                        $aux->cabecalho  = "<strong>USUÁRIOS SELECIONADOS-</strong>";
                                        $aux->codigo     = "id_usuario";
                                        $aux->descr      = "nome";
                                        $aux->nomeobjeto = "usuariossel";
                                        $aux->funcao_js  = 'js_mostradb_usuarios';
                                        $aux->funcao_js_hide = 'js_mostradb_usuarios1';
                                        $aux->func_arquivo = "func_db_usuariosalt.php";
                                        $aux->nomeiframe = "db_iframe_db_usuarios";
                                        $aux->executa_script_apos_incluir = "document.form1.id_usuario.focus();";
                                        $aux->executa_script_lost_focus_campo = "js_insSelectusuariossel();";
                                        $aux->executa_script_change_focus = "document.form1.id_usuario.focus();";
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
                    </table>
                    <input name="relatorio" type="button" value="Relatório" onClick="js_relatorio();">
                </center>
            </form>
        </td>
    </tr>
</table>
<?php db_menu(); ?>
</body>
</html>
<script>

function js_relatorio(){
  form = document.form1;

  js_seleciona_combo(form.usuariossel);

  jan = window.open("","db_usuarios_imprime","width="+(screen.availWidth-5)+",height="+(screen.availHeight-40)+",scrollbars=0,location=0 ");
  document.form1.action = "con2_relatoriov2users_002.php";
  document.form1.target = "db_usuarios_imprime";
  setTimeout("document.form1.submit()",1000);

}
</script>