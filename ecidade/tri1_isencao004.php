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
require_once(modification("classes/db_isencaoinscr_classe.php"));
require_once(modification("classes/db_isencaomatric_classe.php"));
require_once(modification("classes/db_isencaocgm_classe.php"));
require_once(modification("classes/db_isencaoproc_classe.php"));
require_once(modification("classes/db_isencaolanc_classe.php"));
require_once(modification("classes/db_isencaotipo_classe.php"));
require_once modification("libs/JSON.php");

$clisencao         = new cl_isencao;
$clisencaocgm      = new cl_isencaocgm;
$clisencaoinscr    = new cl_isencaoinscr;
$clisencaomatric   = new cl_isencaomatric;
$clisencaoproc     = new cl_isencaoproc;
$clisencaocalc = new cl_isencaocalc;

db_postmemory($_POST);

$db_opcao = 1;
$db_botao = true;

if(isset($incluir)){
  $sqlerro=false;
  db_inicio_transacao();
  $clisencao->v10_usuario = db_getsession('DB_id_usuario');
	$clisencao->v10_dtlan   = date('Y-m-d',db_getsession('DB_datausu'));
  $clisencao->incluir($v10_sequencial);
  $erro_msg = $clisencao->erro_msg; 
  if($clisencao->erro_status==0){
    $sqlerro=true;
  } 
	if(isset($v17_protprocesso) && $v17_protprocesso != ""){
		$clisencaoproc->v17_isencao = $clisencao->v10_sequencial;
		$clisencaoproc->v17_protprocesso = $v17_protprocesso;
		$clisencaoproc->incluir(null);
		if($clisencaoproc->erro_status==0){
			$erro_msg = $clisencaoproc->erro_msg; 
			$sqlerro=true;
		} 
	}
	if($origem == 1){
		$clisencaocgm->v12_isencao = $clisencao->v10_sequencial;
	  $clisencaocgm->v12_numcgm  = $valorigem;
	  $clisencaocgm->incluir(null);
		if($clisencaocgm->erro_status==0){
      $erro_msg = $clisencaocgm->erro_msg; 
			$sqlerro=true;
		} 
	}else	if($origem == 2){
		$clisencaoinscr->v16_isencao = $clisencao->v10_sequencial;
	  $clisencaoinscr->v16_inscr   = $valorigem;
	  $clisencaoinscr->incluir(null);
		if($clisencaoinscr->erro_status==0){
      $erro_msg = $clisencaoinscr->erro_msg; 
			$sqlerro=true;
		} 
	}else if($origem == 3){
		$clisencaomatric->v15_isencao = $clisencao->v10_sequencial;
	  $clisencaomatric->v15_matric  = $valorigem;
	  $clisencaomatric->incluir(null);
		if($clisencaomatric->erro_status==0){
      $erro_msg = $clisencaomatric->erro_msg; 
			$sqlerro=true;
		} 
	}

	if (!$sqlerro) {
      /**
       * Decodifica e insere os calculos liberados
       * para essa isencao isencao
       */
      $oJson = new Services_JSON();
      $calculosLiberados = $oJson->decode(str_replace("\\", "", $_POST['calculosSelecionados']));

      foreach ($calculosLiberados as $calculo) {
        if (isset($calculo)) {
          $sequencialIsencaoCalc = db_utils::fieldsMemory(db_query("(select nextval('isencaocalc_v46_sequencial_seq'))"),0)->nextval;
          
          $clisencaocalc->v46_isencao = $clisencao->v10_sequencial;
          $clisencaocalc->v46_cadcalc = $calculo->q85_codigo;
		  $clisencaocalc->v46_percentual = 100;
          $clisencaocalc->incluir($sequencialIsencaoCalc);

          if($clisencaocalc->erro_status == 0) {
            $sqlerro = true;
          } 
        }
      }
    }

  db_fim_transacao($sqlerro);
  $v10_sequencial = $clisencao->v10_sequencial;
  $db_opcao = 1;
  $db_botao = true;
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<div class="container">
<?php
include(modification("forms/db_frmisencao.php"));
?>
</div>
</body>
</html>
<?php

if(isset($incluir)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($clisencao->erro_campo!=""){
      echo "<script> document.form1.".$clisencao->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clisencao->erro_campo.".focus();</script>";
    };
  }else{
   db_msgbox($erro_msg);
   db_redireciona("tri1_isencao005.php?liberaaba=true&chavepesquisa=$v10_sequencial&origem=$origem&valorigem=$valorigem");
  }
}
?>