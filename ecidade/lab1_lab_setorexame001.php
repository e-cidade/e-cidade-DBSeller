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
include(modification("classes/db_lab_labusuario_classe.php"));
include(modification("classes/db_lab_setorexame_classe.php"));
include(modification("classes/db_lab_horario_classe.php"));
include(modification("classes/db_lab_requiitem_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$cllab_labusuario = new cl_lab_labusuario;
$cllab_setorexame = new cl_lab_setorexame;
$cllab_horario = new cl_lab_horario;
$cllab_requiitem = new cl_lab_requiitem;
$db_opcao = 1;
$db_botao = true;

if(isset($opcao)){
  if( $opcao == "alterar"){
    $db_opcao = 2;
    $db_botao1 = true;
  }else{
    if( $opcao=="excluir" || isset($db_opcao) && $db_opcao==3){
       $db_opcao = 3;
       $db_botao1 = true;
    }else{
       if(isset($alterar)){
          $db_opcao = 2;
          $db_botao1 = true;
       }
    }
  }
}

if(isset($incluir)){

  db_inicio_transacao();
  $error = false;
  $result = $cllab_setorexame->sql_record($cllab_setorexame->sql_query(null, '*', null, ' la09_i_labsetor = '.$la09_i_labsetor.' and la09_i_exame = '.$la09_i_exame .' and la02_i_codigo = '.$la24_i_laboratorio));
  if($cllab_setorexame->numrows>0){ 
    $error = true;
    $errorMessage = "Exame já cadastrado nesse setor.";
  }
  if($error==false){
    $result = $cllab_setorexame->sql_record($cllab_setorexame->sql_query(null, '*', null, ' la09_i_exame = '.$la09_i_exame .' and la02_i_codigo = '.$la24_i_laboratorio));
    if($cllab_setorexame->numrows>0){ 
      $error = true;
      $errorMessage = "Não é possivel incluir o mesmo exame em mais de um setor.";
    }
  }
  if($error==false){
    $cllab_setorexame->incluir(null);
  }
  db_fim_transacao();

}else if(isset($alterar)){
  db_inicio_transacao();
  $result = $cllab_setorexame->sql_record($cllab_setorexame->sql_query(null, '*', null, ' la09_i_codigo <> '.$la09_i_codigo.' and la09_i_exame= '.$la09_i_exame .' and la02_i_codigo = '.$la24_i_laboratorio));
  if($cllab_setorexame->numrows>0){ 
    $error = true;
    $errorMessage = "Não é possivel alterar o exame, pois o mesmo já esta cadastrado em outro setor.";
  }else{
    $db_opcao = 2;
    $cllab_setorexame->alterar($la09_i_codigo);
  }
  db_fim_transacao();

}else if(isset($excluir)){

  db_inicio_transacao();
  $error = false;
  $result = $cllab_horario->sql_record($cllab_horario->sql_query_laboratorio(null, '*', null, 'la09_i_codigo = '.$la09_i_codigo .' and la02_i_codigo = '.$la24_i_laboratorio));
  if($cllab_horario->numrows>0){ 
    $error = true;
    $errorMessage = "Não é possivel excluir o exame deste setor, pois o mesmo está vinculado a um horário.";
  }
  if($error == false){
    $result = $cllab_requiitem->sql_record($cllab_requiitem->sql_query2(null, '*', null, 'la21_i_setorexame = '.$la09_i_codigo .' and la02_i_codigo = '.$la24_i_laboratorio));
    if($cllab_requiitem->numrows>0){ 
      $error = true;
      $errorMessage = "Não é possivel excluir o exame deste setor pois o mesmo está vinculado à, pelo menos, uma requisição. Caso necessario, é possivel alterar a situação para Desativado.";
    }
  }
  if($error == false){
    $db_opcao = 3;
    $cllab_setorexame->excluir($la09_i_codigo);
  }
  
  db_fim_transacao();

}else if(isset($chavepesquisa)){

   $db_opcao = 2;
   $result = $cllab_setorexame->sql_record($cllab_setorexame->sql_query($chavepesquisa));
   if($cllab_setorexame->numrows>0){

      db_fieldsmemory($result,0);
      $db_botao = true;

   }else{

      $la09_i_laboratorio=$chavepesquisa;
      $db_opcao = 1;

   }
}

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
<!--<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>-->
</table>
<center>
<br><br>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
    <fieldset style='width: 75%;'> <legend><b>Setor Exame</b></legend>
	<?php
	include(modification("forms/db_frmlab_setorexame.php"));
	?>
	</fieldset>
    </center>
	</td>
  </tr>
</table>
</center>
<?php
//db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
js_tabulacaoforms("form1","la09_i_setor",true,1,"la09_i_setor",true);
</script>
<?php
if( (isset($incluir)) || (isset($alterar)) || (isset($excluir)) ){
  if($cllab_setorexame->erro_status=="0" && $error == false){
    $cllab_setorexame->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllab_setorexame->erro_campo!=""){
      echo "<script> document.form1.".$cllab_setorexame->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllab_setorexame->erro_campo.".focus();</script>";
    }
  }else{
    if($error){
      echo "<script>alert('$errorMessage')</script>";
    }else{
      $cllab_setorexame->erro(true,false);
      db_redireciona("lab1_lab_setorexame001.php?la24_i_laboratorio=$la24_i_laboratorio&la02_c_descr=$la02_c_descr");
    }
  }
}
?>