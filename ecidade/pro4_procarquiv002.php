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
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_procarquiv_classe.php"));
include(modification("classes/db_procandam_classe.php"));
include(modification("classes/db_arqproc_classe.php"));
include(modification("classes/db_arqandam_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

$clprocarquiv = new cl_procarquiv;
$clprocandam  = new cl_procandam;
$clarqproc    = new cl_arqproc;
$clarqandam   = new cl_arqandam;

$lErro    = false;
$db_opcao = 2;
$db_botao = false;
$iUsuario = db_getsession('DB_id_usuario');

if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"]) == "Alterar") {

  $sSqlArquivamento = $clprocarquiv->sql_query($p67_codarquiv);
  $rsArquivamento   = $clprocarquiv->sql_record($sSqlArquivamento);

  if ($rsArquivamento === false || $clprocarquiv->numrows == 0) {

    $lErro = true;
    $clprocarquiv->erro_status = "0";
    $clprocarquiv->erro_msg    = "Arquivamento não encontrado para edição.";
  }

  $oArquivamento = db_utils::fieldsMemory($rsArquivamento, 0);

  if ($oArquivamento->p67_id_usuario != $iUsuario) {

    $lErro = true;
    $clprocarquiv->erro_status = "0";
    $clprocarquiv->erro_msg    = "Não é possível alterar o Arquivamento. Somente o usuário que realizou o arquivamento tem permissão para alteração.";
  }

  $oDaoProcAndam         = db_utils::getDao('protprocesso');
  $sSqlBuscaProcAndam    = $oDaoProcAndam->sql_query_andam($oArquivamento->p67_codproc, "p58_codandam, p61_dtandam, p61_hora");
  $rsBuscaProcAndam      = $oDaoProcAndam->sql_record($sSqlBuscaProcAndam);
  $oProcAndam            = db_utils::fieldsMemory($rsBuscaProcAndam, 0);


  $dDataProcAndam        = $oProcAndam->p61_dtandam;
  $dHoraProcAndam        = $oProcAndam->p61_hora;

  $oDaoProcAndamInt      = db_utils::getDao('procandamint');
  $sSqlBuscaProcAndamInt = "select p78_data, p78_hora
                                from procandamint
                               where p78_codandam = {$oProcAndam->p58_codandam}
                               order by p78_sequencial desc limit 1";

  $rsBuscaProcAndamInt   = $oDaoProcAndamInt->sql_record($sSqlBuscaProcAndamInt);

  if($oDaoProcAndamInt->numrows > 0) {

    $oProcAndamInt       = db_utils::fieldsMemory($rsBuscaProcAndamInt, 0);
    $dDataProcAndam      = $oProcAndamInt->p78_data;
    $dHoraProcAndam      = $oProcAndamInt->p78_hora;
  }

  $oDataAndamento        = new DBDate($dDataProcAndam." ".$dHoraProcAndam);
  $oDataAlterar          = new DBDate(DBDate::converter($p67_dtarq)." ".db_hora());
  if ($oDataAndamento->calculaIntervaloEntreDatas($oDataAlterar, $oDataAndamento) < 0)
  {

    $lErro                     = true;
    $clprocarquiv->erro_status = "0";
    $clprocarquiv->erro_campo  = "p67_dtarq";
    $clprocarquiv->erro_msg    = "A data para o arquivamento é menor que a data da última movimentação. Verifique a data da sessão!";
  }

  //die($dDataProcAndam." - ".$dHoraProcAndam." ".$p67_dtarq);
  if (!$lErro) {

    db_inicio_transacao();
    $db_opcao = 2;
    $clprocarquiv->alterar($p67_codarquiv);
    $result_andam = $clarqandam->sql_record($clarqandam->sql_query(null,
                                                                   "p69_codandam",
                                                                   null,
                                                                   " p69_codarquiv= " . $clprocarquiv->p67_codarquiv
    )
    );
    if ($clarqandam->numrows > 0) {

      db_fieldsmemory($result_andam, 0);
      $clprocandam->p61_despacho = $p67_historico;
      $clprocandam->p61_codandam = $p69_codandam;
      $clprocandam->p61_dtandam  = $p67_dtarq;
      $clprocandam->p61_hora     = db_hora();
      $clprocandam->alterar($p69_codandam);
    }
    db_fim_transacao();
  }
} else if (isset($chavepesquisa)) {

  $db_opcao = 2;
  $result   = $clprocarquiv->sql_record($clprocarquiv->sql_query($chavepesquisa));

  if ($result != false && $clprocarquiv->numrows > 0) {

    db_fieldsmemory($result, 0);
    $lMesmoUsuario = $p67_id_usuario == $iUsuario;
    $db_botao      = $lMesmoUsuario;
  }
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<div class="container">
  <table width="790" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
	  <?php
	  include(modification("forms/db_frmprocarquiv.php"));
	  ?>
	  </td>
    </tr>
  </table>
</div>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if ($clprocarquiv->erro_status == "0") {

  $clprocarquiv->erro(true, false);
  $db_botao = true;
  echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
  if ($clprocarquiv->erro_campo != "") {

    echo "<script> document.form1.".$clprocarquiv->erro_campo.".style.backgroundColor='#99A9AE';</script>";
    echo "<script> document.form1.".$clprocarquiv->erro_campo.".focus();</script>";
  }
} else {
  $clprocarquiv->erro(true, true);
}
if ($db_opcao == 22) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
