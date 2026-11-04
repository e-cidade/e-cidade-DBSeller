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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/JSON.php");
require_once modification("libs/db_utils.php");

$oJson      = new services_json();
$iCodTarefa = utf8_decode($_POST["string"]);

$sSqlTarefas = "select at40_descr      as cod, 
                       at40_sequencial as label
                  from tarefa 
                       inner join tarefamotivo   on tarefamotivo.at55_tarefa   = tarefa.at40_sequencial
                       left  join tarefaprevisao on tarefaprevisao.at81_tarefa = tarefa.at40_sequencial
                 where tarefaprevisao.at81_sequencial is null
                   and tarefa.at40_progresso < 100 
                   and tarefamotivo.at55_motivo in (2,3)
                   and at40_sequencial like '{$iCodTarefa}%'        
                 order by at40_sequencial ";

$rsTarefa = db_query($sSqlTarefas);
$iNumRows = pg_num_rows($rsTarefa);

$aRetorno = db_utils::getCollectionByRecord($rsTarefa,false,false,true);

echo $oJson->encode($aRetorno);

?>