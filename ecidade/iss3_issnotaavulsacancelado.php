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
require(modification("libs/db_utils.php"));
include(modification("dbforms/db_funcoes.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_issnotaavulsacanc_classe.php"));
include(modification("classes/db_parissqn_classe.php"));
include(modification("classes/db_issnotaavulsaservico_classe.php"));

$clissnotaavulsacanc = new cl_issnotaavulsacanc();
$get                 = db_utils::postmemory($_GET);
$rsNota              = $clissnotaavulsacanc->sql_record($clissnotaavulsacanc->sql_query(null,"*",null,"q63_issnotaavulsa = ".$get->q51_sequencial));
$db_opcao            = 3;
$oNota               = db_utils::fieldsMemory($rsNota,0);
$q63_usuario         = $oNota->q63_usuario;
$nome                = $oNota->nome;
$q63data             = explode("-",$oNota->q63_data);
$q63_data_dia        = $q63data[2];
$q63_data_mes        = $q63data[1];
$q63_data_ano        = $q63data[0];
$q63_motivo          = $oNota->q63_motivo;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<?php
include(modification("forms/db_frmissnotaavulsacancalt.php"));
?>
</body>
</html>