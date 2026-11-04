<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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
require_once(modification("classes/db_fis_lancamento_classe.php"));
require_once(modification("classes/db_fis_levanta_classe.php"));
require_once(modification("classes/db_fis_lanclevanta_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);


$cllancamento  = new cl_fis_lancamento;
$cllevanta     = new cl_fis_levanta;
$cllanclevanta = new cl_fis_lanclevanta;

$db_opcao    = 2;
$db_botao    = true;

global $nl01_codlanc;
global $y39_codandam;

if ( (isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"]) == "alterar" ) {

  try {

    if (!empty($y60_codlev) && !empty($nl01_codlanc)) {

      $sWhere        = " nl15_lancamento = $nl01_codlanc and nl15_levanta = $y60_codlev";
      $sSql          = $cllanclevanta->sql_query_file(null, "*", null, $sWhere);
      $rsAutoLevanta = $cllanclevanta->sql_record($sSql);

      if ($cllanclevanta->numrows >= 1) {
        throw new Exception("Levantamento já cadastrado nesta Notificação de Lançamento!");
      }
    }

    $cllanclevanta->nl15_lancamento    = $nl01_codlanc;
    $cllanclevanta->nl15_levanta = $y60_codlev;
    $cllanclevanta->alterar($nl15_sequencial);

    $erro = $cllanclevanta->erro_msg;
    if ( $cllanclevanta->erro_status == 0 ) {
      $sqlerro = true;
    }
    db_fim_transacao();

  } catch (Exception $oErro) {

    db_fim_transacao(true);
    $cllanclevanta->erro_status = 0;
    $cllanclevanta->erro_msg    = $oErro->getMessage();
  }

} else if (isset($chavepesquisa) && isset($chavepesquisa1)) {

  $sWhere = " nl15_lancamento = $chavepesquisa and nl15_levanta = $chavepesquisa1 ";

  $db_opcao = 2;
  $result   = $cllanclevanta->sql_record($cllanclevanta->sql_query("", "*", "", $sWhere));
  db_fieldsmemory($result,0);

  $rsLevanta = $cllevanta->sql_record($cllevanta->sql_query_pesquisa(null, "*", null, "y60_codlev=$chavepesquisa1"));
  db_fieldsmemory($rsLevanta,0);
  
  $sSql   = $cllancamento->sql_query_cgm_inscricao($nl01_codlanc);
  $rsAuto = $cllancamento->sql_record($sSql);
  db_fieldsmemory($rsAuto,0);

  $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div class="container">
    <?php
      include(modification("forms/db_frm_fis_lanclevanta.php"));
    ?>
  </div>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){

  if($cllanclevanta->erro_status=="0"){

    $cllanclevanta->erro(true,false);
    $db_botao = true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllanclevanta->erro_campo!=""){

      if ($cllanclevanta->erro_campo == 'nl15_levanta') {
        $cllanclevanta->erro_campo = "y60_codlev";
      }

      echo "<script> document.form1.".$cllanclevanta->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllanclevanta->erro_campo.".focus();</script>";
    }
  }else{

    $cllanclevanta->erro(true,false);
       echo "<script>parent.iframe_lanclevanta.location.href='fis1_fis_lanclevanta001.php?nl01_codlanc=$nl01_codlanc';
            parent.iframe_lanctipo.location.href     = 'fis1_fis_lanctipo001.php?nl18_codlanc=".$nl01_codlanc."&abas=1';\n

    </script>";
  }
}
?>
