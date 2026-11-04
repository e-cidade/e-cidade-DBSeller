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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_isencao_classe.php"));
require_once(modification("classes/db_isencaolanc_classe.php"));
require_once(modification("classes/db_isencaocalc_classe.php"));

$clisencao = new cl_isencao;
$clisencaocalc = new cl_isencaocalc;
$db_opcao = 1;

db_postmemory($_POST);
db_postmemory($_SERVER);

$cadcalcValido = isset($q81_cadcalc) && !empty($q81_cadcalc);
$percentualValido = isset($v46_percentual) && !empty($v46_percentual) && (float)$v46_percentual <= 100;

if (isset($incluir) && $cadcalcValido && $percentualValido) {
  $clisencaocalc->excluir(null, "v46_cadcalc = $q81_cadcalc and v46_isencao = $v18_isencao");

  $sequencialIsencaoCalc = db_utils::fieldsMemory(db_query("(select nextval('isencaocalc_v46_sequencial_seq'))"),0)->nextval;

  $clisencaocalc->v46_isencao = $v18_isencao;
  $clisencaocalc->v46_cadcalc = $q81_cadcalc;
  $clisencaocalc->v46_percentual = $v46_percentual;
  $clisencaocalc->incluir($sequencialIsencaoCalc);

  if($clisencaocalc->erro_status == 0) {
    $sqlerro = true;
  } 
}

if (isset($sequencialcalculo) && !empty($sequencialcalculo)) {

  $clisencaocalc->excluir(null, "v46_cadcalc = $sequencialcalculo and v46_isencao = $v18_isencao");

  if($clisencaocalc->erro_status == 0) {
    $sqlerro = true;
  } 
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
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
  <div class="container">
    <?php
    require_once(modification("forms/db_frmisencaocalc.php"));
    ?>
  </div>
</body>

</html>
