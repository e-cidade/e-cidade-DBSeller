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
require(modification("std/db_stdClass.php"));
require(modification("libs/db_conecta.php"));
require(modification("libs/db_sessoes.php"));
//include(modification("dbforms/db_funcoes.php"));

require(modification("libs/JSON.php"));

//var_dump($_POST);

$oJson             = new services_json();
$oParam            = $oJson->decode(db_stdClass::db_stripTagsJson(str_replace("\\","",$_POST["dados"])));
$oRetorno          = new stdClass;
$oRetorno->status  = 1;
$oRetorno->message = "";

if ($oParam->acao == "putsession") {
   
	  if (isset($oParam->dadosSetor)) {
      db_putsession('setor',  $oParam->dadosSetor);
	  }
	  
    if (isset($oParam->dadosQuadra)) { 
      db_putsession('quadra', $oParam->dadosQuadra);
    }
    
    $oRetorno->status = 0;
    $oRetorno->message = "Set Quadra/Lote";
  
}

if ($oParam->acao == "putRua") {
   
    db_putsession('rua',  $oParam->dadosRua);
    
    $oRetorno->status = 0;
    $oRetorno->message = "Set Rua";
  
}

echo $oJson->encode($oRetorno);
?>