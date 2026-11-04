<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
require_once(modification("classes/db_fis_fiscalusuario_classe.php"));
require_once(modification("classes/db_fis_fandamusu_classe.php"));
require_once(modification("classes/db_fis_fandam_classe.php"));
require_once(modification('classes/db_fis_db_usuarios_estendida_classe.php'));
require_once(modification('classes/db_fis_parametrosandamento_classe.php'));
require_once(modification('classes/db_fis_usuariosparametrosandamento_classe.php'));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$db_opcao = 2;
$db_botao = true;

$cldbusuariosestendida = new cl_fis_db_usuarios_estendida;
$clparametrosandamento = new cl_fis_parametrosandamento;
$clusuariosparametrosandamento = new cl_fis_usuariosparametrosandamento;

$sqlerro = false;
if (isset($alterar)) {
    db_inicio_transacao();

    $clusuariosparametrosandamento->parametrosandamento = $parametrosandamento;
    $clusuariosparametrosandamento->db_usuarios = $id_usuario;
    $clusuariosparametrosandamento->alterar($sequencial);

   // if ($clusuariosparametrosandamento->numrows == 0) {
     //   $sqlerro = true;
    //}

    db_fim_transacao($sqlerro);
} elseif (isset($chavepesquisa)){
    $result = $cldbusuariosestendida->sql_record($cldbusuariosestendida->sqlQueryUsuariosParametrosAndamento("distinct(db_usuarios.id_usuario), db_usuarios.nome, fis_usuariosparametrosandamento.sequencial","fis_parametrosandamento.sequencial = $parametrosandamento and db_usuarios.id_usuario = $chavepesquisa"));
    db_fieldsmemory($result,0);

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
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
    <?php 
     require_once(modification("forms/db_frm_fis_tipoandamusu.php"));
    ?>
    </center>
    </td>
  </tr>
</table>
</body>
</html>
<?php 
if (isset($alterar)) {
    if ($clusuariosparametrosandamento->erro_status == '0') {
        $clusuariosparametrosandamento->erro(true,false);
        $db_botao=true;
        if($clusuariosparametrosandamento->erro_campo!=""){
            echo "<script> document.form1.".$clusuariosparametrosandamento->erro_campo.".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1.".$clusuariosparametrosandamento->erro_campo.".focus();</script>";
        }
    } else {
        $clusuariosparametrosandamento->erro(true, false);
        db_redireciona("fis1_fis_tipoandamusu001.php?parametrosandamento=$clusuariosparametrosandamento->parametrosandamento");
    }
}

?> 
