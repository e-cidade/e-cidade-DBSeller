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
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_rhferias_classe.php"));
require_once(modification("classes/db_rhferiasperiodo_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/pessoal/Ferias.model.php"));

$clrhferias            = new cl_rhferias;
$clrhferiasperiodo     = new cl_rhferiasperiodo;

$db_opcao              = 1;
$db_botao              = true;

$oPost                 = db_utils::postMemory($_POST);
$clrhferias            = new cl_rhferias;
$clrhferiasperiodo     = new cl_rhferiasperiodo;
$db_botao              = true;
$db_opcao              = 1;

$rh110_datainicial_dia = null;
$rh110_datainicial_mes = null;
$rh110_datainicial_ano = null;
$rh110_datafinal_dia   = null;
$rh110_datafinal_mes   = null;
$rh110_datafinal_ano   = null;

$clrhferias->rotulo->label();
$clrhferiasperiodo->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("DBtxt23");
$clrotulo->label("DBtxt25");
$rh109_dias = 30;

$rh109_regist  = isset($oPost->rh109_regist) ? $oPost->rh109_regist : null;

if (isset($oPost->rh109_regist)) {
  
  $sSqlNome      = "select z01_nome from cgm inner join rhpessoal on z01_numcgm = rh01_numcgm";
  $sSqlNome     .= " where rh01_regist = {$oPost->rh109_regist} ";
  $rsNome        = $clrhferias->sql_record($sSqlNome);
  $z01_nome      = db_utils::fieldsMemory($rsNome, 0)->z01_nome;
}

if (isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) {
	$rh109_regist = $_SESSION['aListaMatriculasProcessamentoEmLote'][0];
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/dates.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
      <center>
        <?php 
          if(!isset($rh109_regist)) {
            require_once(modification("forms/db_frmcadastroferias.php"));
          } else {
            require_once(modification("forms/db_frmrhferias.php"));
          }
        ?>
      </center>
    </td>
  </tr>
</table>
<?php
if (!isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) {
	db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
}

/**
 * Verifica se há uma variável de sessão com a lista de matrículas para operação em
 * lote com confirmação.
 */
if (isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) {

	$rh109_regist = array_shift($_SESSION['aListaMatriculasProcessamentoEmLote']);
	if (count($_SESSION['aListaMatriculasProcessamentoEmLote']) > 0) {
		asort($_SESSION['aListaMatriculasProcessamentoEmLote']);
	} else {
		unset($_SESSION['aListaMatriculasProcessamentoEmLote']);
	}
}
?>
</body>
</html>
<script>
js_tabulacaoforms("form1","rh109_regist",true,1,"rh109_regist",true);
</script>