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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){

  echo "<script>location.href='fis1_fis_auto005.php?db_opcao=3'</script>";
  exit;
}

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$lancamento = 1;
$cllancamento    = db_utils::getDao("fis_lancamento");
$cllancusu       = db_utils::getDao("fis_lancusu");
$cllanclocal     = db_utils::getDao("fis_lanclocal");
$cllancfiscal    = db_utils::getDao("fis_lancfiscal");
$cllancexec      = db_utils::getDao("fis_lancexec");
$cllancinscr     = db_utils::getDao("fis_lancinscr");
$cllancmatric    = db_utils::getDao("fis_lancmatric");
$cllanccgm       = db_utils::getDao("fis_lanccgm");
$cllancrec       = db_utils::getDao("fis_lancrec");
$cllancandam     = db_utils::getDao("fis_lancandam");
$cllancultandam  = db_utils::getDao("fis_lancultandam");
$clfandam        = db_utils::getDao("fis_fandam");
$cllancsanitario = db_utils::getDao("fis_lancsanitario");
$cllanctipo      = db_utils::getDao("fis_lanctipo");
$cllanctestem    = db_utils::getDao("fis_lanctestem");
$cllancrespons   = db_utils::getDao("fis_lancresponsavel");
$clfandamusu     = db_utils::getDao("fis_fandamusu");
$cllancamentonumpre  = db_utils::getDao("fis_lancamentonumpre");
$clenderecopecas     = db_utils::getDao("fis_enderecopecas");

$db_botao = false;
$db_opcao = 33;

if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){

  db_inicio_transacao();
  $db_opcao = 3;

  $cllanclocal->excluir($nl01_codlanc);
  $cllanclocal->erro(false,false);

  $cllancexec->excluir($nl01_codlanc);
  $cllancexec->erro(false,false);

  $cllanccgm->excluir($nl01_codlanc);
  $cllanccgm->erro(false,false);

  $cllancmatric->excluir($nl01_codlanc);
  $cllancmatric->erro(false,false);

  $cllancinscr->excluir($nl01_codlanc);
  $cllancinscr->erro(false,false);

  $cllancsanitario->excluir($nl01_codlanc);
  $cllancsanitario->erro(false,false);

  $cllancfiscal->excluir($nl01_codlanc);
  $cllancfiscal->erro(false,false);

  $cllancusu->excluir($nl01_codlanc);
  $cllancusu->erro(false,false);

  $result1 = $cllancrec->sql_record($cllancrec->sql_query_file($nl01_codlanc));
  if($cllancrec->numrows > 0){

    $numrows = $cllancrec->numrows;
    for($x=0;$x<$numrows;$x++){
      db_fieldsmemory($result1,$x);
      $cllancrec->y57_codauto = $nl22_codlanc;
      $cllancrec->y57_receit = $nl22_receit;
      $cllancrec->excluir($nl22_codlanc,$nl22_receit);
      $cllancrec->erro(false,false);
    }
  }

  $rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$nl01_codlanc} and end01_tipopeca = 'A'");
  
  $clenderecopecas->end01_codpeca  = $nl01_codlanc;
  $clenderecopecas->end01_tipopeca = "L";
  if( pg_num_rows($rsEndPecas) > 0 ){
    $clenderecopecas->excluir();
  }

  if( $clenderecopecas->erro_status == "0" ){
    $sqlerro = true;
    $erro    = $clenderecopecas->erro_msg;
  }

  $result = $cllancandam->sql_record($cllancandam->sql_query_file("","","nl19_codandam"," nl19_codandam desc"," nl19_codlanc = $nl01_codlanc"));
  if($cllancandam->numrows > 0){
    db_fieldsmemory($result,0);

	$result1 = $clfandamusu->sql_record($clfandamusu->sql_query_file($nl19_codandam));
	if ($clfandamusu->numrows > 0) {
      $numrows = $clfandamusu->numrows;
      for ($x=0;$x<$numrows;$x++) {
	    db_fieldsmemory($result1,$x);
	    $clfandamusu->excluir($y40_codandam,$y40_id_usuario);
	    $clfandamusu->erro(false,false);
      }
    }

    $cllancultandam->excluir($nl01_codlanc);
    $cllancultandam->erro(false,false);
    $numrows = $cllancandam->numrows;
    for($x=0;$x<$numrows;$x++){
      db_fieldsmemory($result,$x);
      $cllancandam->excluir($nl01_codlanc,$nl19_codandam);
      $cllancandam->erro(false,false);
      $clfandam->excluir($nl19_codandam);
      $clfandam->erro(false,false);
    }
  }

  $result1 = $cllanctipo->sql_record($cllanctipo->sql_query(null, "*", "", "nl18_codlanc = ".$nl01_codlanc));
  if($cllanctipo->numrows > 0){
    $numrows = $cllanctipo->numrows;
    for($x=0;$x<$numrows;$x++){
      db_fieldsmemory($result1,$x);
      $cllanctipo->excluir($nl18_codigo);
      $cllanctipo->erro(false,false);
    }
  }

  $cllanctestem->excluir($nl01_codlanc);
  $cllancrespons->excluir($nl01_codlanc);
  $cllancamento->excluir($nl01_codlanc);
  $msg = $cllancamento->erro_msg;

  db_fim_transacao();
} else if (isset($chavepesquisa)) {
   $db_opcao = 3;
   $result = $cllancamento->sql_record($cllancamento->sql_query($chavepesquisa,"*",null," nl01_instit = ".db_getsession('DB_instit') ));
   db_fieldsmemory($result,0);
   $result = $cllanclocal->sql_record($cllanclocal->sql_query($chavepesquisa,"*"));
   if($cllanclocal->numrows > 0){
     db_fieldsmemory($result,0);
   }
   $result = $cllancexec->sql_record($cllancexec->sql_query($chavepesquisa,"*"));
   if($cllancexec->numrows > 0){
     db_fieldsmemory($result,0);
   }

   //Verifica se o auto foi calculado
   $result_numpre = $cllancamentonumpre->sql_record($cllancamentonumpre->sql_query(null,"*",null," fis_lancamento.nl01_codlanc = $chavepesquisa "));
   if ($cllancamentonumpre->numrows > 0) {
     db_fieldsmemory($result_numpre,0);
     db_msgbox('Alteração não Permitida!\n\nNotificação de Lançamento já calculado, numpre do calculo: '.$y17_numpre.'\n\nDeve ser acessada a rotina de Baixa de Notificação de Lançamento de Infração em Procedimentos > Notificação de Lançamento de Infração > Baixa > Inclusão');
	 db_redireciona('fis1_fis_lancamento003.php?abas=1');
   }

   $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div class="container">
  	<?php
  	  include(modification("forms/db_frm_fis_lancamento.php"));
  	?>
  </div>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){
   db_msgbox($msg);
   echo " <script>
            parent.iframe_auto.location.href='fis1_fis_lancamento003.php?abas=1';
         </script> ";
}
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
