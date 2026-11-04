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
require_once(modification("classes/db_fis_lanctipo_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_lancandam_classe.php"));
require_once(modification("classes/db_fis_lancultandam_classe.php"));
require_once(modification("classes/db_fis_fandam_classe.php"));
require_once(modification("classes/db_fis_fiscalprocrec_classe.php"));
require_once(modification("classes/db_fis_lancmulta_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$cllanctipo      = new cl_fis_lanctipo;
$cllancandam     = new cl_fis_lancandam;
$cllancultandam  = new cl_fis_lancultandam;
$clfandam        = new cl_fis_fandam;
$clfiscalprocrec = new cl_fis_fiscalprocrec;
$cllancmulta     = new cl_fis_lancmulta;
$db_opcao = 22;
$db_botao = false;
global $y39_codandam;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){

  try {

    db_inicio_transacao();
    $db_opcao = 2;
    


    if(!empty($nl18_codtipo_old) && !empty($nl18_codtipo)){

      $cllanctipo->excluir(null,"nl18_codlanc=$nl18_codlanc and nl18_codtipo=$nl18_codtipo_old");

      if ( strpos(trim($nl18_valor),',') != "" ){

        $nl18_valor = str_replace('.','',$nl18_valor);
        $nl18_valor = str_replace(',','.',$nl18_valor);
      }
      $cllanctipo->nl18_valor = "$nl18_valor";
      $cllanctipo->nl18_codlanc=$nl18_codlanc;
      $cllanctipo->nl18_codtipo=$nl18_codtipo;
      $cllanctipo->incluir(null);
    }

    if(!empty($nl28_codtipo_old) && !empty($n218_codtipo)){

      $cllancmulta->excluir(null,"nl28_codlanc=$nl18_codlanc and nl28_codtipo=$nl28_codtipo_old");

      if ( strpos(trim($nl18_valor),',') != "" ){

        $nl28_valor = str_replace('.','',$nl28_valor);
        $nl28_valor = str_replace(',','.',$nl28_valor);
      }
      $cllancmulta->nl28_valor   = $nl18_valor;
      $cllancmulta->nl28_codlanc = $nl18_codlanc;
      $cllancmulta->nl28_codtipo = $nl18_codtipo;
      if( isset($cllanctipo->nl18_codigo) ){
        $cllancmulta->nl28_lanctipo = $cllanctipo->nl18_codigo;
      }
      $cllancmulta->incluir(null);
      
    }
    db_fim_transacao();
    $db_botao = true;

  } catch (Exception $oErro) {

    db_fim_transacao(true);
    $cllanctipo->erro_status = 0;
    $cllanctipo->erro_msg    = $oErro->getMessage();
  }
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $cllanctipo->sql_record($cllanctipo->sql_query(null,"*",null,"nl18_codlanc=$chavepesquisa and nl18_codtipo=$chavepesquisa1"));   
   db_fieldsmemory($result,0);
   $db_botao = true;
   $sCampos = " nl28_codlanc as nl18_codlanc, nl18_codtipo, b.y29_descr as y29_descr, nl18_valor, nl28_codtipo,    a.y29_descr as y29_descr2, nl28_valor ";
   $rsMultaQuery = $cllancmulta->sql_record( $cllancmulta->sql_query_lancmultatipo( $sequencial , $sCampos ,null , "nl28_codlanc = $chavepesquisa" ) );
   if( $cllancmulta->numrows > 0 ){
      db_fieldsmemory( $rsMultaQuery, 0);
   }
}
// die($cllanctipo->erro_status);
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

       include(modification("forms/db_frm_fis_lanctipo.php"));
     ?>
    </div>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($cllanctipo->erro_status=="0"){
    $cllanctipo->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllanctipo->erro_campo!=""){
      echo "<script> document.form1.".$cllanctipo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllanctipo->erro_campo.".focus();</script>";
    }else{
      echo "<script>parent.iframe_lanctipo.location.href='fis1_fis_lanctipo001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
      echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl18_codlanc=".$nl18_codlanc."&y39_codandam=".$y39_codandam."&abas=1';</script>\n";
    }
  }else{
    echo "<script>parent.iframe_lanctipo.location.href='fis1_fis_lanctipo001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl18_codlanc=".$nl18_codlanc."&y39_codandam=".$y39_codandam."&abas=1';</script>\n";
  }
}
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
