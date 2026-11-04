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
require_once(modification("classes/db_fis_lancandam_classe.php"));
require_once(modification("classes/db_fis_lancrec_classe.php"));
require_once(modification("classes/db_fis_lancultandam_classe.php"));
require_once(modification("classes/db_fis_lancusu_classe.php"));
require_once(modification("classes/db_fis_fandam_classe.php"));
require_once(modification("classes/db_fis_fandamusu_classe.php"));
require_once(modification("classes/db_fis_fiscalprocrec_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_lancmulta_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$cllanctipo      = new cl_fis_lanctipo;
$cllancandam     = new cl_fis_lancandam;
$cllancrec       = new cl_fis_lancrec;
$cllancultandam  = new cl_fis_lancultandam;
$cllancusu       = new cl_fis_lancusu;
$clfandam        = new cl_fis_fandam;
$clfandamusu     = new cl_fis_fandamusu;
$clfiscalprocrec = new cl_fis_fiscalprocrec;
$cllancmulta     = new cl_fis_lancmulta;

$db_botao = false;
$db_opcao = 33;
global $nl18_codlanc;
$nl18_codlanc = @$nl01_codlanc;

if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){


  db_inicio_transacao();
  $db_opcao = 3;


  $result = $clfiscalprocrec->sql_record($clfiscalprocrec->sql_query_lanctipo("",""," distinct y45_receit,y45_codtipo,y45_descr,y45_valor",""," nl18_codlanc = $nl18_codlanc"));
  if($clfiscalprocrec->numrows > 0){

    $numrows = $clfiscalprocrec->numrows;
    for($y=0;$y<$numrows;$y++){
      db_fieldsmemory($result,$y);
      $result1 = $cllancrec->sql_record($cllancrec->sql_query_file($nl18_codlanc));
      $num = $cllancrec->numrows;
      if($cllancrec->numrows > 0){
        for($x=0;$x<$num;$x++){
          db_fieldsmemory($result1,$x);
	        if($nl22_receit == $y45_receit){
            $cllancrec->nl22_codlanc = $nl22_codlanc;
            $cllancrec->nl22_receit = $nl22_receit;
            $cllancrec->excluir($nl22_codlanc,$nl22_receit);
	        }
        }
      }
    }
  }

  $result = $cllancandam->sql_record($cllancandam->sql_query_file("","","nl19_codandam",""," nl19_codlanc = $nl18_codlanc"));
  if($cllancandam->numrows == 1){

    db_fieldsmemory($result,0);
    $clfandamusu->excluir($nl19_codandam);
   // $cllancusu->excluir($nl18_codlanc);
    $cllancultandam->excluir($nl18_codlanc,$nl19_codandam);
    $cllancandam->excluir($nl18_codlanc,$nl19_codandam);
    $clfandam->excluir($nl19_codandam);
  }
  if(isset($nl28_codtipo) && !empty($nl28_codtipo)){
    $cllancmulta->excluir(null,"nl28_codlanc=$nl18_codlanc and nl28_codtipo=$nl28_codtipo");
  }
  if(isset($nl18_codtipo) && !empty($nl18_codtipo)){
    $cllanctipo->excluir(null,"nl18_codlanc=$nl18_codlanc and nl18_codtipo=$nl18_codtipo");
  }

  db_fim_transacao();
  //location.href='fis1_fis_lanctipo001.php?nl18_codlanc=60
  echo "<script>parent.iframe_receitas.location.href='fis1_fis_lancrec001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
  echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl18_codlanc=".$nl18_codlanc."&y39_codandam=".$y39_codandam."&abas=1';</script>\n";
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 3;
   $result   = $cllanctipo->sql_record($cllanctipo->sql_query(null,"*",null,"nl18_codlanc=$chavepesquisa and nl18_codtipo=$chavepesquisa1"));
   db_fieldsmemory($result,0);
   $db_botao = true;
   $sCampos = " nl28_codlanc as nl18_codlanc, nl18_codtipo, b.y29_descr as y29_descr, nl18_valor, nl28_codtipo,    a.y29_descr as y29_descr2, nl28_valor ";
   $rsMultaQuery = $cllancmulta->sql_record( $cllancmulta->sql_query_lancmultatipo( $sequencial , $sCampos ) );
   if( $cllancmulta->numrows > 0 ){
      db_fieldsmemory( $rsMultaQuery, 0);
   }
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
  	 include(modification("forms/db_frm_fis_lanctipo.php"));
  	?>
  </div>
</body>
</html>
<script type="text/javascript">
js_tabulacaoforms("form1","db_opcao",true,1,"db_opcao",true);
</script>
<?php 
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){
  if($cllanctipo->erro_status=="0"){
    $cllanctipo->erro(true,false);
  }else{
    $cllanctipo->erro(true,false);
    echo "<script>parent.iframe_lanctipo.location.href='fis1_fis_lanctipo001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl18_codlanc=".$nl18_codlanc."&y39_codandam=".$y39_codandam."&abas=1';</script>\n";
  };
};
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
