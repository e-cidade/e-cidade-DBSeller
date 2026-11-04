<?php
/*
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
require_once(modification("classes/db_fis_lanctipo_classe.php"));
require_once(modification("classes/db_fis_lancandam_classe.php"));
require_once(modification("classes/db_fis_lancultandam_classe.php"));
require_once(modification("classes/db_fis_fandam_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_lancrec_classe.php"));
require_once(modification("classes/db_fis_fiscalprocrec_classe.php"));

db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);

$cllanctipo      = new cl_fis_lanctipo; // lancatipo
$cllancandam     = new cl_fis_lancandam; // lancandam
$clfandam        = new cl_fis_fandam;
$cllancultandam  = new cl_fis_lancultandam; // lancultandam
$clfiscalprocrec = new cl_fis_fiscalprocrec;
$cllancrec       = new cl_fis_lancrec; // lancrec
$db_opcao = 1;
$db_botao = true;
global $nl18_codlanc;
global $y39_codandam;
$nl18_codlanc = @$y50_codlanc;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){

  try {

    if (!empty($nl18_codtipo) && !empty($nl18_codlanc)) {

      $sWhere        = " nl18_codtipo = $nl18_codtipo and nl18_codlanc = $nl18_codlanc";
      $sSql          = $cllanctipo->sql_query_file(null, "*", null, $sWhere);
      $rslancLevanta = $cllanctipo->sql_record($sSql);

      if ($cllanctipo->numrows >= 1) {
        throw new Exception("Procedência já cadastrada neste lanc de Infração!");
      }
    }

    db_inicio_transacao();

    if ($nl18_fator==""){
      $cllanctipo->nl18_fator='0';
    }
    if (strpos(trim($nl18_valor),',')!=""){
	   $nl18_valor=str_replace('.','',$nl18_valor);   
	   $nl18_valor=str_replace(',','.',$nl18_valor);
    }
    $cllanctipo->nl18_valor   = $nl18_valor;
    $cllanctipo->nl18_codlanc = $nl18_codlanc;
    $cllanctipo->nl18_codtipo = $nl18_codtipo;
    $cllanctipo->incluir(null);

    if($cllanctipo->erro_status != 0 && $cllanctipo->numrows <= 1){

      $clfandam->y39_codtipo = $andamento;
      $clfandam->y39_obs="0";
      $clfandam->y39_id_usuario= db_getsession("DB_id_usuario");
      $clfandam->y39_data=date("Y-m-d",db_getsession("DB_datausu"));
      $clfandam->y39_hora=db_hora();
      $clfandam->incluir(null);

      $cllancultandam->y16_codlanc = $nl18_codlanc;
      $cllancultandam->y16_codandam = $clfandam->y39_codandam;
      $cllancultandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
      $cllancandam->y58_codlanc = $nl18_codlanc;
      $cllancandam->y58_codandam = $clfandam->y39_codandam;
      $cllancandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
    }

    if(isset($nl18_codlanc) && $nl18_codlanc != ""){
        $result = $cllancrec->sql_record($cllancrec->sql_query_file($nl18_codlanc));
        if($cllancrec->numrows == 0){
            $result = $clfiscalprocrec->sql_record($clfiscalprocrec->sql_query_lanctipo("",""," *",""," nl18_codlanc = $nl18_codlanc"));
            if($clfiscalprocrec->numrows > 0){
                $numrows = $clfiscalprocrec->numrows;
                for($i=0;$i<$numrows;$i++){
                    db_fieldsmemory($result,$i);
                    $cllancrec->y57_valor = $y45_valor;
                    $cllancrec->y57_descr = $y45_descr;
                    $cllancrec->incluir($nl18_codlanc,$y45_receit);
                }
            }
        }
    } 
	
    db_fim_transacao();

  } catch (Exception $oErro) {

    db_fim_transacao(true);
    $cllanctipo->erro_status = 0;
    $cllanctipo->erro_msg    = $oErro->getMessage();
  }
}elseif(isset($andamento) && $andamento != ""){
  db_inicio_transacao();
  $result = $cllancultandam->sql_record($cllancultandam->sql_query("",""," max(y16_codandam) as y16_codandam ",""," y16_codlanc = $nl18_codlanc and y50_instit = ".db_getsession('DB_instit') ));
  if($cllancultandam->numrows > 0){

    db_fieldsmemory($result,0);
    $clfandam->y39_codtipo    = $andamento;
    $clfandam->y39_obs        = "0";
    $clfandam->y39_id_usuario = db_getsession("DB_id_usuario");
    $clfandam->y39_data       = date("Y-m-d",db_getsession("DB_datausu"));
    $clfandam->y39_hora       = db_hora();
    $clfandam->alterar($y16_codandam);
  }else{

    $clfandam->y39_codtipo    = $andamento;
    $clfandam->y39_obs        = "0";
    $clfandam->y39_id_usuario = db_getsession("DB_id_usuario");
    $clfandam->y39_data       = date("Y-m-d",db_getsession("DB_datausu"));
    $clfandam->y39_hora       = db_hora();
    $clfandam->incluir();

    $cllancultandam->y16_codlanc  = $nl18_codlanc;
    $cllancultandam->y16_codandam = $clfandam->y39_codandam;
    $cllancultandam->incluir($nl18_codlanc,$clfandam->y39_codandam);

    $cllancandam->y58_codlanc  = $nl18_codlanc;
    $cllancandam->y58_codandam = $clfandam->y39_codandam;
    $cllancandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
  }
db_fim_transacao();
}
//die($nl18_codlanc);
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
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){
  if($cllanctipo->erro_status=="0"){
    $cllanctipo->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllanctipo->erro_campo!=""){
      echo "<script> document.form1.".$cllanctipo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllanctipo->erro_campo.".focus();</script>";
    };
  }else{
    $cllanctipo->erro(true,false);
    echo "<script>parent.iframe_lanctipot.location.href='fis1_fis_lanctipo001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
    //echo "<script>parent.iframe_receitas.location.href='fis1_fis_lancrec001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl18_codlanc=".$nl18_codlanc."&y39_codandam=".$clfandam->y39_codandam."&abas=1';</script>\n";
  };
}
?>
