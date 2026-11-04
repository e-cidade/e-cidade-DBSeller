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
require_once(modification('classes/db_fis_db_depart_estendida_classe.php'));
require_once(modification('classes/db_fis_parametrosandamento_classe.php'));
require_once(modification('classes/db_fis_deptosparametrosandamento_classe.php'));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$db_opcao = 1;
$db_botao = true;

$cldbdeptosestendida = new cl_fis_db_depart_estendida;
$clparametrosandamento = new cl_fis_parametrosandamento;
$cldeptosparametrosandamento = new cl_fis_deptosparametrosandamento;

$sqlerro = false;
if (isset($incluir)) {
    db_inicio_transacao();

    $cldeptosparametrosandamento->parametrosandamento = $parametrosandamento;
    $cldeptosparametrosandamento->db_depart = $coddepto;
    $cldeptosparametrosandamento->incluir(); 
    
    $erro_msg = $cldeptosparametrosandamento->erro_msg;

    if ($cldeptosparametrosandamento->erro_status == '0') {
        $sqlerro = true;   
    }

    db_fim_transacao($sqlerro);
}

/*$result = $cldbdeptosestendida->sql_record($cldbdeptosestendida->sqlQueryDeptosParametrosAndamentos("distinct(db_depart.coddepto), db_depart.descrdepto","db_depart.instit = ".db_getsession('DB_instit')." and parametrosandamento = ".@$parametrosandamento, 'db_depart.coddepto'));
db_fieldsmemory(@$result, 0);
*/
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC onLoad="" >
<!-- <table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center> -->
  <?php 
   require_once(modification("forms/db_frm_fis_tipoandamdeptos.php"));
  ?>
    <!-- </center>
  </td>
  </tr>
</table> -->
</body>
</html>
<?php
if (isset($incluir)) {
    if ($cldeptosparametrosandamento->erro_status == '0') {
        $cldeptosparametrosandamento->erro(true,false);
        $db_botao=true;
        if($cldeptosparametrosandamento->erro_campo!=""){
            echo "<script> document.form1.coddepto.style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1.coddepto.focus();</script>";
        }
    } else {
        $cldeptosparametrosandamento->erro(true, false);
        db_redireciona("fis1_fis_tipoandamdeptos001.php?parametrosandamento=$cldeptosparametrosandamento->parametrosandamento");
    }
}

?> 
