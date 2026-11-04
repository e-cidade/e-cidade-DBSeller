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

require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("classes/db_fis_parfiscal_classe.php");
require_once  modification("classes/db_fis_fiscalparametros_classe.php");
require_once  modification("dbforms/db_funcoes.php");
require_once  modification('classes/db_fis_tipoprocessoadministrativo_classe.php');
require_once  modification('classes/db_fis_parnotificacaolancamento_classe.php');

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);

$clparfiscal = new cl_fis_parfiscal;
$clfiscalparametros = new cl_fis_fiscalparametros;
$clparnotificacaolancamento  = new cl_fis_parnotificacaolancamento;
$cltipoprocessoadministrativo = new cl_fis_tipoprocessoadministrativo;
$db_opcao    = 22;
$db_botao    = false;
$lSqlErro    = false;
$y32_instit  = db_getsession('DB_instit');

if (isset($alterar)) {

  try {

      db_inicio_transacao();

      $y32_instit = db_getsession('DB_instit');

      if( $utilizadocpadrao == 0 ){
        $HTTP_POST_VARS['y32_templateautoinfracao'] = "null";
        $y32_templateautoinfracao                   = "null";
      }

      if ($y32_modalvara != 3) {

        $HTTP_POST_VARS['y32_templatealvarasanitarioprovisorio'] = "null";
        $HTTP_POST_VARS['y32_templatealvarasanitariopermanente'] = "null";
        $y32_templatealvarasanitarioprovisorio                   = "null";
        $y32_templatealvarasanitariopermanente                   = "null";
      } else if (empty($y32_templatealvarasanitarioprovisorio)) {
        throw new Exception("Campo Template Padrão Alvará Sanitário Provisório não informado.");
      }else if (empty($y32_templatealvarasanitariopermanente)) {
        throw new Exception("Campo Template Padrão Alvará Sanitário Permanente não informado.");
      }

      $result = $clparfiscal->sql_record($clparfiscal->sql_query_param());
      if($result == false || $clparfiscal->numrows == 0) {
        $clparfiscal->incluir($y32_instit);
      } else {
        $clparfiscal->alterar($y32_instit);
      }

      if ($clparfiscal->erro_status == '0') {
        throw new Exception($clparfiscal->erro_msg);
      }

      $clfiscalparametros->pa01_autodvenc  = $pa01_autodvenc;
      $clfiscalparametros->pa01_autodprazo = $pa01_autodprazo;
      $clfiscalparametros->pa01_instit     = $y32_instit;

      $sqlFiscalParam = $clfiscalparametros->sql_query_file($y32_instit,"*");
      $resultParam    = $clfiscalparametros->sql_record($sqlFiscalParam);

      if ($clfiscalparametros->numrows > 0) {
        $clfiscalparametros->alterar($y32_instit);
      }else{
        $clfiscalparametros->incluir($y32_instit);
      }

      if($clfiscalparametros->erro_status == '0') {
        throw new Exception($clfiscalparametros->erro_msg);
      }

      $cltipoprocessoadministrativo->sequencial = $sequencial;
      $cltipoprocessoadministrativo->verificatipo = $verificatipo;
      if(!empty($sequencial)) {
        $cltipoprocessoadministrativo->alterar();
      }else{
        $cltipoprocessoadministrativo->incluir();
      }
      if ($cltipoprocessoadministrativo->erro_status == '0') {
        throw new Exception($cltipoprocessoadministrativo->erro_msg);
      }

      $clparnotificacaolancamento->nl27_tipo = $nl27_tipo;
      $clparnotificacaolancamento->nl27_historico = $nl27_historico;
      $clparnotificacaolancamento->nl27_procbaixaauto = $nl27_procbaixaauto;
      $clparnotificacaolancamento->nl27_utilizadocpadrao = $nl27_utilizadocpadrao;
      $clparnotificacaolancamento->nl27_templateautoinfracao = $nl27_templateautoinfracao;
      $clparnotificacaolancamento->nl27_autodvenc = $nl27_autodvenc;
      $clparnotificacaolancamento->nl27_autodprazo = $nl27_autodprazo;

      $resultParNot = db_query($clparnotificacaolancamento->sql_query($y32_instit,"*",null,""));

      if ($resultParNot != false && pg_num_rows($resultParNot) > 0) {
        $clparnotificacaolancamento->alterar($y32_instit);
      }else{
        $clparnotificacaolancamento->incluir($y32_instit);
      }

      if ($clparnotificacaolancamento->erro_status == '0') {
        throw new Exception($clparnotificacaolancamento->erro_msg);
      }
      db_fim_transacao(false);
  } catch (\Exception $erro) {

    db_fim_transacao(true);

    $clparfiscal->erro_status = "0";
    $clparfiscal->erro_msg = $erro->getMessage();
  }
} else {

  $result  = $clparfiscal->sql_record($clparfiscal->sql_query_param($y32_instit,"*",null,""));

  if ($result != false && $clparfiscal->numrows > 0) {
    db_fieldsmemory($result,0);
  }

  $result2 = $clparnotificacaolancamento->sql_record($clparnotificacaolancamento->sql_query($y32_instit,
               "fis_parnotificacaolancamento.*,
                k00_descr as nl27_tipo_descr,
                k01_descr as nl27_hist_descr,
                db82_descricao as nl27_descricaoautodeinfracao
               ",null,""));

  if ($result2 != false && $clparnotificacaolancamento->numrows > 0) {
    db_fieldsmemory($result2,0);
  }

  $sqlPuginParam = 'select * from fiscalizacao.fis_fiscalparametros';
  $rsPluginParam = db_query($sqlPuginParam);
  db_fieldsmemory($rsPluginParam, 0);

  $sqlProc = "select * from fiscalizacao.fis_tipoprocessoadministrativo";
  $rsProc = db_query($sqlProc);
  db_fieldsmemory($rsProc, 0);


}

$db_opcao = 2;
$db_botao = true;
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body class="body-default">
    <div class="container">
      <?php
    	  include modification("forms/db_frm_fis_parfiscal.php");
    	?>
    </div>
    <?php
      db_menu( db_getsession("DB_id_usuario"),
               db_getsession("DB_modulo"),
               db_getsession("DB_anousu"),
               db_getsession("DB_instit") );
    ?>
  </body>
</html>
<?php

if (isset($alterar)) {

  if ($clparfiscal->erro_status == "0") {

    $clparfiscal->erro(true, false);
    $db_botao = true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";

    if ($clparfiscal->erro_campo != "") {

      echo "<script> document.form1.".$clparfiscal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clparfiscal->erro_campo.".focus();</script>";
    }
  } else {
    $clparfiscal->erro(true,true);
  }
}

if ($db_opcao == 22) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
