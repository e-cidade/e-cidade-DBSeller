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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_stdlibwebseller.php"));

$oParametrosConfiguracao = loadConfig('sau_config');
$iModelo                 = $oParametrosConfiguracao->s103_i_modelofaa;
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
  <link type="text/css" href="estilos.css" rel="stylesheet">
  <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css" rel="stylesheet"/>
  <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css" rel="stylesheet"/>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
</head>
<body>
    <div id="cntTriagem" class="container"></div>
</body>
</html>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script language="JavaScript" type="text/javascript" src="scripts/classes/saude/ambulatorial/DBViewTriagem.classe.js"></script>
<script type="text/javascript" language="JavaScript" src="scripts/AjaxRequest.js"> </script>
<script>
$.noConflict();
const oTriagem = new DBViewTriagem( DBViewTriagem.prototype.TELA_TRIAGEM );
oTriagem.temProntuario( true );
oTriagem.bloqueiaCartaoSus( true );
oTriagem.exibirBotaoEmitirFAA( true, <?=$iModelo;?> );
oTriagem.origemAgenda( true );
oTriagem.show($('cntTriagem'));
oTriagem.consultarFaa();

</script>