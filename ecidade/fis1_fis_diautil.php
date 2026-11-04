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

require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");

db_postmemory($HTTP_POST_VARS);
$erro = 0;

if (!isset($paramNotificacao)){
   $paramNotificacao = false;
};

$diavencimento = explode("/", $vencim);
$diaprazo      = explode("/", $prazor);
$ciencia       = explode("/", $ciencia);
$diavencimento = $diavencimento[2]."-".$diavencimento[1]."-".$diavencimento[0];
$diaprazo      = $diaprazo[2]."-".$diaprazo[1]."-".$diaprazo[0];
$diaciencia    = $ciencia[2]."-".$ciencia[1]."-".$ciencia[0];
$sParam = db_query("select pa01_autodprazo,pa01_autodvenc as pa01_autodvenc  from fiscalizacao.fis_fiscalparametros");

if ($paramNotificacao) {
  
  $queryNotificacao  = "select nl27_autodvenc as pa01_autodvenc,nl27_autodprazo as pa01_autodprazo";
  $queryNotificacao .= " from fiscalizacao.fis_parnotificacaolancamento where nl27_instit = ".db_getsession('DB_instit');

  $sParam = db_query($queryNotificacao);
} 

db_fieldsmemory($sParam,0);
$rsPrazodt      = db_query("select fc_proximo_dia_util(cast('".$diaciencia."' as date)+1) as dataciencia");
db_fieldsmemory($rsPrazodt,0);
$rsPrazo      = db_query("select fc_proximo_dia_util(cast('".$dataciencia."' as date)+".$pa01_autodvenc.") as diavencimento2");
if( pg_num_rows( $rsPrazo ) > 0 ){
  db_fieldsmemory($rsPrazo,0);
  $rsVencimento = db_query("select fc_proximo_dia_util('".$diavencimento2."') as vencimento");

  if(pg_num_rows($rsVencimento) > 0){
    db_fieldsmemory($rsVencimento,0);
    $sRetornoV = explode("-", $vencimento);
    $sRetornoV_dia = $sRetornoV[2];
    $sRetornoV_mes = $sRetornoV[1];
    $sRetornoV_ano = $sRetornoV[0];
  }
}

$rsPrazo = db_query("select fc_proximo_dia_util(cast('".$diaciencia."' as date)+1) as dataciencia");
if( pg_num_rows( $rsPrazo ) > 0 ){
  db_fieldsmemory($rsPrazo,0);
  $rsDias      = db_query("select fc_proximo_dia_util(cast('".$dataciencia."' as date)+".$pa01_autodprazo.") as recurso");
  db_fieldsmemory($rsDias,0);
  $sRetornoP   = explode("-", $recurso);
  $sRetornoP_dia = $sRetornoP[2];
  $sRetornoP_mes = $sRetornoP[1];
  $sRetornoP_ano = $sRetornoP[0];
}

echo json_encode(
		array(
			'erro'               => $erro,
      'sRetornoP_dia'      => $sRetornoP_dia,
			'sRetornoP_mes'      => $sRetornoP_mes,
      'sRetornoP_ano'      => $sRetornoP_ano,
      'sRetornoV_dia'      => $sRetornoV_dia,
      'sRetornoV_mes'      => $sRetornoV_mes,
			'sRetornoV_ano'      => $sRetornoV_ano
			)
		);
