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
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
$oJson             = new services_json();
$oParam            = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno          = new stdClass();
$oRetorno->status  = 1;
$oRetorno->message = '';

// $oParam->dataoriginal;
// $oParam->reduzido;

$data = $oParam->dataoriginal;	
$dataNova = date('Y-m-d', strtotime("-30 days",strtotime($data)));

$sqlDadosContaDatas = " select k68_sequencial as k68_sequencial,
                                                 k68_data
                                             from concilia
                                            where k68_contabancaria = {$oParam->reduzido} and
                                                  k68_data <= '".$dataNova."'
                                            order by k68_data desc limit 1";

$rsDadosContaDatas   = db_query($sqlDadosContaDatas);

if (pg_num_rows($rsDadosContaDatas) == 0){

$sqlDadosContaDatas = " select k68_sequencial as k68_sequencial,
                                                 k68_data
                                             from concilia
                                            where k68_contabancaria = {$oParam->reduzido} and
                                                  k68_data >= '".$dataNova."'
                                            order by k68_data asc limit 1";

$rsDadosContaDatas   = db_query($sqlDadosContaDatas);
}
db_fieldsmemory($rsDadosContaDatas,0);

$oRetorno->oSequencial = $k68_sequencial;
$oRetorno->oData = $k68_data;

echo $oJson->encode($oRetorno);
?>
