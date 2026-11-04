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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
require_once(modification('classes/db_fis_fiscal_estendida_classe.php'));
include(modification("classes/db_fis_fiscaltipo_classe.php"));
include(modification("classes/db_fis_fiscalandam_classe.php"));
include(modification("classes/db_fis_fiscalultandam_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("classes/db_fis_fandamusu_classe.php"));
include(modification("classes/db_fis_fiscalusuario_classe.php"));
require_once(modification('classes/db_fis_datacienciaandamento_classe.php'));
include(modification("classes/db_fis_fiscalrua_classe.php"));
include(modification("classes/db_fis_fiscbairro_classe.php"));
include(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
  echo "<script>location.href='fis3_fis_fandamnoti005.php".((isset($intimacao)) ? '?intimacao=1' : '')."'</script>";
  exit;
}

// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
$labelCode     = ((isset($intimacao) && $intimacao == 1) ? 'Código da Intimação' : 'Código da Notificação');
// -------------

$clrotulo        = new rotulocampo;
$clfiscal = new cl_fis_fiscal_estendida;
$clfiscaltipo = new cl_fis_fiscaltipo;
$clfiscalandam = new cl_fis_fiscalandam;
$clfiscalultandam = new cl_fis_fiscalultandam;
$clfandam        = new cl_fis_fandam;
$clfandamusu     = new cl_fis_fandamusu;
$clfiscalusuario   = new cl_fis_fiscalusuario;
$clfiscalrua   = new cl_fis_fiscalrua;
$clfiscbairro  = new cl_fiscbairro;
$cldatacienciaandamento = new cl_fis_datacienciaandamento;

$clrotulo->label("y39_codandam");
$clrotulo->label("y30_codnoti");
$db_opcao = 1;
$db_botao = true;
$bloqueia = $db_opcao;

if(isset($y30_codnoti) && !isset($HTTP_POST_VARS["db_opcao"])){
   $db_opcao = 3;

   $sSqlGetProcessoAdministrativo  = " select p58_numero || '/' || p58_ano as p58_numero, p58_requer from fiscalizacao.fis_fiscal ";
   $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalnotificacao on y110_notificacaofiscal = y30_codnoti ";
   $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscal on y110_procfiscal = y100_sequencial ";
   $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalprot on y105_procfiscal = y100_sequencial ";
   $sSqlGetProcessoAdministrativo .= " inner join protprocesso on y105_protprocesso = p58_codproc ";
   $sSqlGetProcessoAdministrativo .= " where y30_codnoti = $y30_codnoti ";

   //die( $sSqlGetProcessoAdministrativo);

   $rsGetProcessoAdministrativo = db_query($sSqlGetProcessoAdministrativo);
   if (pg_numrows($rsGetProcessoAdministrativo) > 0){
     db_fieldsmemory($rsGetProcessoAdministrativo,0);
   }


  $dataAtual = date('Y-m-d');
  if (isset($intimacao) and $intimacao == 1){
    // Checa se a peça for intimação
    $whereIntimacao = ' and y30_codnoti in (select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = true) ';
    // Checa se não tiver tipo de andamento ou se tipo de andamento for de intimação
    $whereIntimacao .= ' and (y39_codandam is null or fis_grupotipoandamento.tipo_peca = 4 ) ';
  } else {
    // Checa se a peça for notificação
    $whereIntimacao = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = false ) ';
    // Checa se não tiver tipo de andamento ou se tipo de andamento for de notificação
    $whereIntimacao .= ' and (y39_codandam is null or fis_grupotipoandamento.tipo_peca = 2 ) ';
  }

  // Se for vinculada a processo fiscal, checa se o fiscal está ativo no processo se sua data limite é válida e se o usuário logado é mesmo o fis_fiscal...
  // ... se não estiver vinculada, checa se o usuário logado está vinculado à intimação ou notificação.
  $whereIntimacao .= " and case when y100_sequencial is not null then fis_processofiscalativo.ativo = 't' and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') and fis_processofiscalativo.fiscal = ".db_getsession('DB_id_usuario')." else fis_fiscalusuario.y38_id_usuario = ".db_getsession('DB_id_usuario')." end ";

$result = $clfiscal->sql_record($clfiscal->getIntimacaoOuNotificacao(null,"fis_fiscal.*","y30_codnoti"," y30_codnoti = ".$y30_codnoti." and y30_setor=".db_getsession("DB_coddepto")." and y30_instit = ".db_getsession('DB_instit').$whereIntimacao ));

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
     $result = $clfiscalusuario->sql_record($clfiscalusuario->sql_query($y30_codnoti));
     if($clfiscalusuario->numrows == 0){
       $db_opcao = 1;
       echo "<script>alert('Não existem fiscais cadastrados para esta notificação!');</script>";
       include(modification("fis3_fis_fandamnoti004.php"));
       exit;
     }
     $db_botao = false;
   }else{
     $db_opcao = 1;
     echo "<script>alert('".$labelCode." inválido!');</script>";
     include(modification("fis3_fis_fandamnoti004.php"));
     exit;
   }
}
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){
db_inicio_transacao();
  $sqlerro = false;
  $clfandam->incluir($y39_codandam);
  $erro=$clfandam->erro_msg;
  if($clfandam->erro_status==0){
    $sqlerro = true;
  }
  $clfiscalultandam->y19_codnoti=$y30_codnoti;
  $clfiscalultandam->excluir($y30_codnoti);
  $clfiscalultandam->incluir($y30_codnoti,$clfandam->y39_codandam);
  $clfiscalandam->incluir($y30_codnoti,$clfandam->y39_codandam);
  $erro=$clfiscalultandam->erro_msg;
  $sProcFiscal  = "select fis_procfiscal.* from fiscalizacao.fis_fiscal inner join fiscalizacao.fis_procfiscalnotificacao on y30_codnoti = y110_notificacaofiscal ";
  $sProcFiscal .= "inner join fiscalizacao.fis_procfiscal on y110_procfiscal = y100_sequencial where y30_codnoti = $y30_codnoti ";

 $rsProcFiscal = db_query($sProcFiscal);
  db_fieldsmemory($rsProcFiscal,0);
  if ( pg_num_rows($rsProcFiscal) > 0 && ($y100_dtinicial == '' or $y100_dtinicial == null)) {
          $iUpdate = 0;
    $sFandam  = " select * from fiscalizacao.fis_grupotipoandamento ";
    $sFandam .= " inner join fiscalizacao.fis_grupotipoandamento_tipoandam on fi30_grupo = sequencial ";
    $sFandam .= " inner join fiscalizacao.fis_tipoandam on fi30_tipoandam = y41_codtipo ";
    $sFandam .= " where y41_codtipo = $y39_codtipo ";

    $rsFandam = pg_query($sFandam);
    //$rsFandam = $clfandam->sql_record($sFandam);
          if (pg_num_rows($rsFandam) > 0) {
                  for($i = 0; $i < pg_num_rows($rsFandam); $i++){
                          db_fieldsmemory($rsFandam, $i);
                          if ($sequencial == 7 or $sequencial == 9) {
                                  $iUpdate = 1; break;
                          }
                  }
          }
          if ($iUpdate == 1){
      $aDataCiencia = explode('/', $data_ciencia);

      $sDataCiencia = $aDataCiencia[2].'-'.$aDataCiencia[1].'-'.$aDataCiencia[0];
                  $sDataProcFiscal = " update fiscalizacao.fis_procfiscal set y100_dtinicial = '$sDataCiencia' ";
      $sDataProcFiscal .= " where y100_sequencial =  $y100_sequencial ";
                  db_query($sDataProcFiscal);

                  $sql = "INSERT INTO fiscalizacao.fis_processoprorrogacaofinalizacao (
                                        processo_fiscal,
                                        data_abertura,
                                        data_prorrogacao,
                                        data_fim,
                                        id_usuario,
                                        situacao,
                                        observacao
                                        ) VALUES (
                                        ".$y100_sequencial.",
                                        '".date("Y-m-d")."',
                                        null,
                                        '".date("Y-m-d",strtotime("+31 days",strtotime($sDataCiencia)))."',
                                        ".db_getsession('DB_id_usuario').",
                                        0,
                                        ''
                                        )";
                        $rsInsert = db_query($sql);
                        if($rsInsert == false){
                            $sqlerro = true;
                            $erro_msg = "Erro ao Incluir Data Final do Processo Fiscal.".pg_last_error();
                        }
          }
  }

  if($clfiscalultandam->erro_status==0){
    $sqlerro = true;
  }

  if ($sqlerro == false){
    if($y39_codtipo == 17 or $y39_codtipo == 19) {
      $cldatacienciaandamento->codigo_peca = $y30_codnoti;
      $cldatacienciaandamento->data_ciencia = $data_ciencia;
      $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
      $cldatacienciaandamento->incluir();
    }
  }

  if ($cldatacienciaandamento->erro_status == '0') {
    $sqlerro = true;
  }
  db_fim_transacao($sqlerro);
}
if(!isset($pri)){
  include(modification("fis3_fis_fandamnoti004.php"));
  exit;
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" bgcolor="#cccccc" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="40" align="center" valign="top" bgcolor="#CCCCCC">
    <fieldset>
    <legend align="center"><?=mb_strtoupper($getLabel)?></legend>
    <center>
    <?php
      db_ancora(@$labelCode,"js_fiscal(true);",1);
      db_input('y30_codnoti',20,$Iy30_codnoti,true,'text',3,"")
    ?>
    </center>
    </fieldset>
  </td>
  </tr>
  <tr>
    <td height="100%" align="center" width="100%" valign="top" bgcolor="#CCCCCC">
    <fieldset>
    <legend align="center">ANDAMENTO</legend>
    <center>
  <?php
  $db_opcao=1;
        $db_botao = true;
  require_once(modification(Modification::getFile("forms/db_frm_fis_fandam.php")));
  ?>
    </center>
    </fieldset>
  </td>
  </tr>
</table>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){
  if($clfandam->erro_status=="0"){
    $clfandam->erro(true,false);
    $db_botao=true;
    if($clfandam->erro_campo!=""){
      echo "<script> document.form1.".$clfandam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfandam->erro_campo.".focus();</script>";
    };
  }else{
    if($sqlerro == true){
      db_msgbox($erro);
    }else{
      $clfandam->erro(true,false);
      // echo "<script>parent.iframe_fiscais.location.href='fis3_fis_fandamnotiusu001.php?y38_codnoti=$y30_codnoti&y39_codandam=".$clfandam->y39_codandam."';</script>";
      // echo "<script>parent.mo_camada('fiscais');</script>";
      // echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
      // echo "<script>parent.iframe_fandam.location.href='fis3_fis_fandamnoti002.php?abas=1&y30_codnoti=$y30_codnoti&chavepesquisa=".$clfandam->y39_codandam."$getIntimacao';</script>";
       // echo "<script>parent.corpo.location.href=='fis3_fis_fandamnoti001.php';</script>";
       // header('Location:fis3_fis_fandamnoti001.php');
       echo "<script>parent.parent.corpo.location.href='fis3_fis_fandamnoti001.php';</script>";
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
