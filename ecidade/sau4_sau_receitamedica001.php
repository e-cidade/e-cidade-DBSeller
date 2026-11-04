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
require_once(modification("classes/db_sau_receitamedica_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
$oDaoSauReceitaMedica       = new cl_sau_receitamedica();
$oDaoFarTipoReceita         = new cl_far_tiporeceita();
$oDaoSauFormaAdmMedicamento = new cl_sau_formaadmmedicamento();
$oDaoDbDocumentoTemplate    = new cl_db_documentotemplate();

$db_opcao    = 1;
$db_botao    = true;
$lImpedirAlt = false;

if (isset($chavepesquisa)) {

 $sSql = $oDaoSauReceitaMedica->sql_query_prontuario($chavepesquisa);
 $rs   = $oDaoSauReceitaMedica->sql_record($sSql);
 if ($oDaoSauReceitaMedica->numrows > 0) {

   db_fieldsmemory($rs, 0);
   $db_opcao    = 2;
   $lImpedirAlt = $s158_i_situacao == 1 ? false : true; // Receitas atendidas ou anuladas não podem ser alteradas
 }

}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta charset="iso-8859-1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
<script type="text/javascript" language="JavaScript" src="scripts/AjaxRequest.js"> </script>
</head>
<body onLoad="a=1">
  <div class="container">
    <fieldset style='width: 75%;'> <legend><b>Receita Médica</b></legend>
      <?php
      require_once(modification('forms/db_frmsau_receitamedica.php'));
      ?>
    </fieldset>
  </div>
</body>
</html>
<script>
js_tabulacaoforms("form1","s158_i_profissional",true,1,"s158_i_profissional",true);
</script>