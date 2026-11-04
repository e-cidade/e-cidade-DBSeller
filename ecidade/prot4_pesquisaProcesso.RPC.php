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

require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta_plugin.php"));
require_once(modification("libs/db_sessoes.php"));
include(modification("classes/db_protprocesso_classe.php"));

$oJson               = new services_json();
$oParam              = $oJson->decode(db_stdClass::db_stripTagsJson(str_replace("\\","",$_POST["json"])));
$clprotprocesso = new cl_protprocesso;

$oRetorno            = new stdClass();
$oRetorno->status    = 1;
$oRetorno->message   = 1;
$lErro               = false;
$sMensagem           = "";


try {

	switch($oParam->exec) {
		
		case 'getProcesso' :

		$iProcesso = $oParam->iProcesso;			
		$numero = explode("/",$iProcesso);

		$p58_numero = $numero[0];

		$p58_ano = $numero[1];
		
		if($p58_ano == null)
			$p58_ano = date("Y");
	

		  $sWhereProcesso = "p58_numero = '{$p58_numero}' and  p58_ano = $p58_ano and p58_instit = ".db_getsession("DB_instit")." ";
			$sqlProcesso   = $clprotprocesso->sql_query_file(null, "*", null, $sWhereProcesso);
			
			$rsProcesso     = $clprotprocesso->sql_record($sqlProcesso);
		  
		  if(pg_num_rows($rsProcesso) > 0){
		  	db_fieldsMemory($rsProcesso,0);
			$oRetorno->iStatus   = 1;
			$oRetorno->iProcesso = $p58_numero."/".$p58_ano;
			$oRetorno->iCodProc  = $p58_codproc;
			$oRetorno->descr     = urlencode($p58_requer);
		  }	else {
		  	$oRetorno->status  = 2;
		  }	
			
		break;	
		
	}

	$oRetorno->sDados = "";
	echo $oJson->encode($oRetorno); 
 
	

} catch (Exception $oErro){

  $oRetorno->status  = 2;
  $oRetorno->message = $oErro->getMessage();
  echo $oJson->encode($oRetorno); 
}
	
?>