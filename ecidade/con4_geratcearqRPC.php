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
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/JSON.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));

$oLayoutTxt = new cl_db_layouttxt;

$oJson    = new services_json();
$oParam   = $oJson->decode(str_replace("\\","",$_POST["json"]));

if ($oParam->exec == "getDadosArquivos") {

  $sCampos    = "db50_codigo, db50_descr, db56_descr, db50_layouttxtgrupo";

  $sWhere = "db56_layouttxtgrupotipo = 2 ";
  $ano = db_getsession("DB_anousu");

  if ($ano >= 2019) {
    $sWhere .= " AND db50_codigo NOT IN (33,34)";
  } else if ($ano == 2018) {
    $sWhere .= " AND db50_codigo <> 34 ";
  } else {
    $sWhere .= " AND db50_codigo <> 301 ";
  }

  $rsArquivos = $oLayoutTxt->sql_record($oLayoutTxt->sql_query(null,$sCampos,"db50_codigo", $sWhere));

  if ($rsArquivos) {

    $aArquivos = db_utils::getCollectionByRecord($rsArquivos,false,false,true);

  } else {

   $sMensagem = "Arquivos nao encontrados";
   $iStatus   = 2;
   $aArquivos = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));

  }

  echo $oJson->encode($aArquivos);
}
?>
