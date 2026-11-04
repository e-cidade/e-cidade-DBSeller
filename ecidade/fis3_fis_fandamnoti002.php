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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
  echo "<script>location.href='fis3_fis_fandamnoti005.php?db_opcao=2".((isset($intimacao)) ? '&intimacao=1' : '')."'</script>";
  exit;
}
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_fis_fiscal_classe.php"));
include(modification("classes/db_fis_fiscaltipo_classe.php"));
include(modification("classes/db_fis_fiscalandam_classe.php"));
include(modification("classes/db_fis_fiscalultandam_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("classes/db_fis_fandamusu_classe.php"));
include(modification("classes/db_fis_fiscalrua_classe.php"));
include(modification("classes/db_fis_fiscbairro_classe.php"));
include(modification("classes/db_fis_fiscalusuario_classe.php"));require_once(modification('classes/db_fis_datacienciaandamento_classe.php'));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);

// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');$labelCode     = ((isset($intimacao) && $intimacao == 1) ? 'Código da Intimação' : 'Código da Notificação');
// -------------

$clrotulo        = new rotulocampo;
$clfiscal     = new cl_fis_fiscal;
$clfiscaltipo = new cl_fis_fiscaltipo;
$clfiscalandam = new cl_fis_fiscalandam;
$clfiscalultandam = new cl_fis_fiscalultandam;
$clfandam        = new cl_fis_fandam;
$clfandamusu     = new cl_fis_fandamusu;
$clfiscalusuario   = new cl_fis_fiscalusuario;
$clfiscalrua   = new cl_fis_fiscalrua;
$clfiscbairro  = new cl_fiscbairro;$cldatacienciaandamento = new cl_fis_datacienciaandamento;
$clrotulo->label("y39_codandam");
$clrotulo->label("y30_codnoti");
$db_botao = false;
$pesqandam=1;
echo "<script>parent.document.formaba.fiscais.disabled=true;</script>";
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  $sqlerro=false;
  $clfandam->alterar($y39_codandam);
	$sProcFiscal  = "select fis_procfiscal.* from fiscalizacao.fis_fiscal inner join fiscalizacao.fis_procfiscalnotificacao on y30_codnoti = y110_notificacaofiscal ";
	$sProcFiscal .= "inner join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial where y30_codnoti = $y30_codnoti ";
	$rsProcFiscal = $clfiscal->sql_record($sProcFiscal);
	db_fieldsmemory($rsProcFiscal,0);
	if ($y100_dtinicial == '' or $y100_dtinicial == null) {
                $iUpdate = 0;
                $sFandam  = " select sequencial from fiscalizacao.fis_grupotipoandamento ";
                $sFandam .= " inner join fiscalizacao.fis_grupotipoandamento_tipoandam on fi30_grupo = sequencial ";
                $sFandam .= " inner join fiscalizacao.fis_tipoandam on fi30_tipoandam = y41_codtipo ";
                $sFandam .= " where y41_codtipo = $y39_codtipo ";
                $rsFandam = $clfandam->sql_record($sFandam);
                if ($clfandam->numrows > 0) {
                        for($i = 0; $i < $clfandam->numrows; $i++){
                                db_fieldsmemory($rsFandam, $i);
                                if ($sequencial == 7 or $sequencial == 9) {
                                        $iUpdate = 1; break;
                                }
                        }
                }
                if ($iUpdate == 1){
                        $aDataCiencia = explode('/', $data_ciencia);
                        $sDataCiencia = $data_ciencia[2].'-'.$data_ciencia[1].'-'.$data_ciencia[0];
                        $sDataProcFiscal = " update fiscalizacao.fis_procfiscal set y100_dtinicial = '$sDataCiencia' ";
                        $sDataProcFiscal .= " where y100_sequencial =  $y100_sequencial ";
                        db_query($sDataProcFiscal);
                }
        }

  $erro=$clfandam->erro_msg;
  if($clfandam->erro_status==0){
    $sqlerro = true;
  }
  if ($sqlerro == false){
    if (isset($sequencial) and $sequencial != ''){
    $cldatacienciaandamento->codigo_peca = $y30_codnoti;
    $cldatacienciaandamento->data_ciencia = $data_ciencia;
    $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
    $cldatacienciaandamento->alterar($sequencial);
  } else {
  $cldatacienciaandamento->codigo_peca = $y30_codnoti;
  $cldatacienciaandamento->data_ciencia = $data_ciencia;
  $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
  $cldatacienciaandamento->incluir();
  }
  }
  if ($cldatacienciaandamento->erro_status == '0') {
    $sqlerro = true;
  }db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clfandam->sql_record($clfandam->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);
   $result = $clfiscalandam->sql_record($clfiscalandam->sql_query("","","*",""," fis_fiscalandam.y49_codandam = $chavepesquisa"));
   db_fieldsmemory($result,0);
   $y19_codandam = $y49_codandam;
   $db_botao = false;
   $result = $clfiscal->sql_record($clfiscal->sql_query($y30_codnoti,"*"));
   if($clfiscal->numrows > 0){
     db_fieldsmemory($result,0);
     $result = $clfiscalrua->sql_record($clfiscalrua->sql_query($y30_codnoti));
     if($clfiscalrua->numrows > 0){
       db_fieldsmemory($result,0);
     }
     $result = $clfiscbairro->sql_record($clfiscbairro->sql_query($y30_codnoti));
     if($clfiscbairro->numrows > 0){
       db_fieldsmemory($result,0);
     }
     $resultDataCienciaAndamento = $cldatacienciaandamento->sql_record($cldatacienciaandamento->sql_query(null, '*', null, 'codigo_peca = '.$y30_codnoti.' and fandam = '.$y39_codandam));
        if($cldatacienciaandamento->numrows > 0){
          db_fieldsmemory($resultDataCienciaAndamento,0);
        }$result = $clfiscalusuario->sql_record($clfiscalusuario->sql_query($y30_codnoti));
     if($clfiscalusuario->numrows == 0){
       $db_opcao = 22;
       echo "<script>alert('Não existem fiscais cadastrados para esta notificação!');</script>";
       echo "<script>location.href='fis3_fis_fandamnoti002.php?abas=1$getIntimacao';</script>";
       exit;
       $db_botao = false;
     }
     $db_botao = false;

      $sSqlGetProcessoAdministrativo  = " select p58_numero || '/' || p58_ano as p58_numero, p58_requer from fiscalizacao.fis_fiscal ";
      $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalnotificacao on y110_notificacaofiscal = y30_codnoti ";
      $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscal on y110_procfiscal = y100_sequencial ";
      $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalprot on y105_procfiscal = y100_sequencial ";
      $sSqlGetProcessoAdministrativo .= " inner join protprocesso on y105_protprocesso = p58_codproc ";
      $sSqlGetProcessoAdministrativo .= " where y30_codnoti = $y30_codnoti ";

      $rsGetProcessoAdministrativo = db_query($sSqlGetProcessoAdministrativo);
      if (pg_numrows($rsGetProcessoAdministrativo) > 0){
         db_fieldsmemory($rsGetProcessoAdministrativo,0);
      }

   }
   echo "<script>parent.iframe_fiscais.location.href='fis3_fis_fandamnotiusu001.php?y39_codandam=$y19_codandam&y38_codnoti=$y30_codnoti';</script>";
   echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <center>
    <td width="100%" align="center" valign="top" bgcolor="#CCCCCC">
    <fieldset width="100%">
    <legend align="center"><?=mb_strtoupper($getLabel)?></legend>
    <?php
      db_ancora(@$labelCode,"js_fiscal(true);",1);
      db_input('y30_codnoti',20,$Iy30_codnoti,true,'text',3,"")
    ?>
    </fieldset>
    </td>
  </tr>
  <tr>
    <td height="230" width="100%" align="center" valign="top" bgcolor="#CCCCCC">
    <fieldset>
    <legend align="center">ANDAMENTO</legend>
	<?php
	$db_opcao=2;
        if($db_opcao==2 && !isset($chavepesquisa)){
	  $db_opcao=22;
        }
        $db_botao = true;
	require_once(modification(Modification::getFile("forms/db_frm_fis_fandam.php")));
        if($db_opcao==22 && !isset($chavepesquisa)){
          echo "<script>document.form1.pesquisar.click();</script>";
        }
	?>
    </fieldset>
	</td>
    </center>
  </tr>
</table>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($clfandam->erro_status=="0"){
    $clfandam->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clfandam->erro_campo!=""){
      echo "<script> document.form1.".$clfandam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfandam->erro_campo.".focus();</script>";
    };
  }else{
    if($sqlerro==true){
      db_msgbox($erro);
    }else{
      $clfandam->erro(true,false);
      echo "<script>parent.iframe_fiscais.location.href='fis3_fis_fandamnotiusu001.php?y39_codandam=$y39_codandam&y38_codnoti=$y30_codnoti';</script>";
      echo "<script>parent.mo_camada('fiscais');</script>";
      echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
      echo "<script>parent.iframe_fandam.location.href='fis3_fis_fandamnoti002.php?abas=1&y30_codnoti=$y30_codnoti&chavepesquisa=".$y39_codandam."$getIntimacao';</script>";
    }
  };
};
?>
<script>
function js_fiscal(mostra){
  var noti=document.form1.y30_codnoti.value;
  js_OpenJanelaIframe('','db_iframe','fis3_fis_fiscal006.php?y30_codnoti='+noti+'<?=$getIntimacao?>','Consulta',true,0);
}
</script>
