<?php
/**
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
require_once  modification("classes/db_fis_levvalor_classe.php");
require_once  modification("classes/db_fis_levantanotas_classe.php");
require_once  modification("classes/db_fis_levanta_classe.php");
require_once  modification("classes/db_fis_levvalorpgtos_classe.php");
require_once  modification("classes/db_fis_valordefla_classe.php");
require_once  modification("dbforms/db_funcoes.php");

db_postmemory($HTTP_POST_VARS);

$cllevvalor      = new cl_fis_levvalor;
$cllevantanotas  = new cl_fis_levantanotas;
$cllevanta       = new cl_fis_levanta;
$cllevvalorpgtos = new cl_fis_levvalorpgtos;
$clvalordefla    = new cl_fis_valordefla;
$db_opcao = 1;
$db_botao = true;
if(isset($incluir)){
  // print_r($_POST); exit;
  // $y63_pago  = Deflacionar_valores($y63_dtvenc, $y68_pgto, $y63_pago, db_getsession('DB_instit'));
  // die($y63_pago);
  $sTotal   = 0;
  if( $valores != '' ){
    $sValores = array();
    $matriz01 = explode('HHH',$valores);
    for( $i=0 ; $i < count( $matriz01 ); $i++ ){
      $matriz = explode( '-', $matriz01[$i]);
      if( $matriz[1] != null ){
        $sValores[$i]['sData'] = substr($matriz[1],6,4)."-".substr($matriz[1],3,2)."-".substr($matriz[1],0,2);
        $dtdefl = $matriz[1];
      }else{
        $dtdefl = $y68_pgto;
      }
      $sValores[$i]['sValor']  =  Deflacionar_valores($y63_dtvenc, $dtdefl, $matriz[0], db_getsession('DB_instit'));
      $sValores[$i]['oValor']  = $matriz[0];
      $sTotal += Deflacionar_valores($y63_dtvenc, $dtdefl, $matriz[0], db_getsession('DB_instit'));
    }
  }
  $sqlerro = false;
  db_inicio_transacao();
  // if($sTotal != 0){
    $y63_pago = ($sTotal == 0 ? $y63_pago : $sTotal);
    $bruto = (($y63_bruto*$y63_aliquota)/100);
    $y63_saldo2 = ($bruto-$y63_pago);
    // die($y63_saldo2);
    $y63_saldo2 = (trim($y63_saldo2) < 0  ? 0 : $y63_saldo2  );
    $cllevvalor->y63_saldo = trim($y63_saldo2);
    // die($cllevvalor->y63_saldo);
  // }
  $cllevvalor->y63_pago = (($y63_pago == '') ? '0' : $y63_pago);
  $cllevvalor->incluir(null);
  $erro_msg = $cllevvalor->erro_msg;
  $y63_sequencia= $cllevvalor->y63_sequencia;
  if( $cllevvalor->erro_status==0 ){
    $sqlerro = true;
  }

  if( !$sqlerro && $valores != '' ){
    foreach ($sValores as $key => $sDados) {
      $result55 = $cllevvalorpgtos->sql_record($cllevvalorpgtos->sql_query_file($y63_sequencia,""," max(y68_seq) +1 as seq"));
      db_fieldsmemory($result55,0);
      $y68_seq = $seq == ""?"1":$seq;
      $cllevvalorpgtos->y68_sequencia = $y63_sequencia;
      $cllevvalorpgtos->y68_seq       = $y68_seq;
      $cllevvalorpgtos->y68_valor     = ($sDados['sValor'] == "" ? '0' : $sDados['sValor'] );
      if(isset($sDados['sData'])){
        $cllevvalorpgtos->y68_pgto = $sDados['sData'];
      }

      $cllevvalorpgtos->incluir($y63_sequencia,$y68_seq);
      $erro_msg=$cllevvalorpgtos->erro_msg;
      if($cllevvalorpgtos->erro_status==0){
        db_msgbox($erro_msg);
        $sqlerro=true;
      }
      if( $sqlerro == false){
        $clvalordefla->incluir($y63_sequencia,$y68_seq,$sDados['oValor']);
        $erro_msg = $clvalordefla->erro_msg;
        if($clvalordefla->erro_status==0){
          db_msgbox($erro_msg);
          $sqlerro = true;
        }
      }
    }
  }
  //rotina para incluir na tabela levantanotas
  if(!$sqlerro && $notas!=''){

    $matriz01 = explode('HHH',$notas);
    for($i=0; $i<count($matriz01); $i++){

      $matriz = explode('_sep_',$matriz01[$i]);
      $data1 = explode("/",$matriz[2]);
      $data  = $data1[2].str_pad($data1[1],2,"0",STR_PAD_LEFT).str_pad($data1[0],2,"0",STR_PAD_LEFT);
      $cllevantanotas->y79_documento = $matriz[0];
      $cllevantanotas->y79_valor     = $matriz[1];
      $cllevantanotas->y79_sequencia = $y63_sequencia;
      $cllevantanotas->y79_data      = $data;
      $cllevantanotas->y79_ordem     = ($i+1);
      $cllevantanotas->incluir(null);
      $erro_msg=$cllevantanotas->erro_msg;
      if($cllevantanotas->erro_status==0){

        $sqlerro = true;
        break;
      }
    }
  }
  db_fim_transacao($sqlerro);

}else if(isset($alterar)){
  $sqlerro=false;
  db_inicio_transacao();
  $sTotal   = 0;
  if( $valores != '' ){
    $sValores = array();
    $matriz01 = explode('HHH',$valores);
    for( $i=0 ; $i < count( $matriz01 ); $i++ ){
      $matriz = explode( '-', $matriz01[$i]);
      if( $matriz[1] != null ){
        $sValores[$i]['sData'] = substr($matriz[1],6,4)."-".substr($matriz[1],3,2)."-".substr($matriz[1],0,2);
        $dtdefl = $matriz[1];
      }else{
        $dtdefl = $y68_pgto;
      }
      $sValores[$i]['sValor']  =  Deflacionar_valores($y63_dtvenc, $dtdefl, $matriz[0], db_getsession('DB_instit'));
      $sValores[$i]['oValor']  = $matriz[0];
      $sTotal += Deflacionar_valores($y63_dtvenc, $dtdefl, $matriz[0], db_getsession('DB_instit'));
    }
  }
  // if($sTotal != 0){
  $y63_pago = ($sTotal == 0 ? $y63_pago : $sTotal);
  $bruto = (($y63_bruto*$y63_aliquota)/100);
  $y63_saldo2 = ($bruto-$y63_pago);
  $y63_saldo2 = (trim($y63_saldo2) < 0  ? 0 : $y63_saldo2  );
  // die($y63_saldo2);
  $cllevvalor->y63_saldo = trim($y63_saldo2);

  // }
  $cllevvalor->y63_pago = (($y63_pago == '') ? '0' : $y63_pago);
  //rotina para alterar a tabela levvalor
  $cllevvalor->alterar($y63_sequencia);
  $erro_msg=$cllevvalor->erro_msg;
  if($cllevvalor->erro_status==0){

    $sqlerro=true;
  }
  //rotina para que caso já tenha registro na tabela levvalorespgtos apagar esses registros
  if(!$sqlerro){

    $result55 = $cllevvalorpgtos->sql_record($cllevvalorpgtos->sql_query_file($y63_sequencia,"","y68_seq"));
    if($cllevvalorpgtos->numrows>0){

      $cllevvalorpgtos->excluir($y63_sequencia);
      $erro_msg=$cllevvalorpgtos->erro_msg;
      if($cllevvalorpgtos->erro_status==0){
        $sqlerro=true;
      }
       if( $sqlerro == false){
        $clvalordefla->excluir($y63_sequencia);
        $erro_msg = $clvalordefla->erro_msg;
        if($clvalordefla->erro_status==0){
          db_msgbox($erro_msg);
          $sqlerro = true;
        }
      }
    }
  }
  //rotina para incluir na tabela levvalorpgtos

  if( !$sqlerro && $valores != '' ){
    foreach ($sValores as $key => $sDados) {
      $result55 = $cllevvalorpgtos->sql_record($cllevvalorpgtos->sql_query_file($y63_sequencia,""," max(y68_seq) +1 as seq"));
      db_fieldsmemory($result55,0);
      $y68_seq = $seq == ""?"1":$seq;
      $cllevvalorpgtos->y68_sequencia = $y63_sequencia;
      $cllevvalorpgtos->y68_seq       = $y68_seq;
      $cllevvalorpgtos->y68_valor     = $sDados['sValor'];
      if(isset($sDados['sData'])){
        $cllevvalorpgtos->y68_pgto = $sDados['sData'];
      }

      $cllevvalorpgtos->incluir($y63_sequencia,$y68_seq);
      $erro_msg=$cllevvalorpgtos->erro_msg;
      if($cllevvalorpgtos->erro_status==0){
        db_msgbox($erro_msg);
        $sqlerro=true;
      }
      if( $sqlerro == false){
        $clvalordefla->incluir($y63_sequencia,$y68_seq,$sDados['oValor']);
        $erro_msg = $clvalordefla->erro_msg;
        if($clvalordefla->erro_status==0){
          db_msgbox($erro_msg);
          $sqlerro = true;
        }
      }
    }
  }

  //rotina para que caso já tenha registro na tabela levvalorespgtos apagar esses registros
  if(!$sqlerro){

    $result = $cllevantanotas->sql_record($cllevantanotas->sql_query_file(null,"*","","y79_sequencia=$y63_sequencia"));
    if($cllevantanotas->numrows>0){

      $cllevantanotas->excluir("","y79_sequencia = $y63_sequencia");
      $erro_msg=$cllevantanotas->erro_msg;
      if($cllevantanotas->erro_status==0){
        $sqlerro=true;
      }
    }
  }
  //rotina para incluir na tabela levantanotas
  if($sqlerro==false && $notas!=''){

    $matriz01 = explode('HHH',$notas);
    for($i=0; $i<count($matriz01); $i++){

      $matriz = explode('_sep_',$matriz01[$i]);
      $data1 = explode("/",$matriz[2]);
      $data  = $data1[2].str_pad($data1[1],2,"0",STR_PAD_LEFT).str_pad($data1[0],2,"0",STR_PAD_LEFT);
      $cllevantanotas->y79_documento = $matriz[0];
      $cllevantanotas->y79_documento = $matriz[0];
      $cllevantanotas->y79_valor     = $matriz[1];
      $cllevantanotas->y79_sequencia = $y63_sequencia;
      $cllevantanotas->y79_data      = $data;
      $cllevantanotas->y79_ordem     = ($i+1);
      $cllevantanotas->incluir(null);
      $erro_msg=$cllevantanotas->erro_msg;
      if($cllevantanotas->erro_status==0){

        $sqlerro=true;
        break;
      }
    }
  }
    if($sqlerro){
      $opcao='alterar';
    }
  db_fim_transacao($sqlerro);
}else if(isset($excluir)){
  $sqlerro = false;
  db_inicio_transacao();
  //rotina para que caso já tenha registro na tabela levvalorespgtos apagar esses registros
  if(!$sqlerro){

    $result55 = $cllevvalorpgtos->sql_record($cllevvalorpgtos->sql_query_file($y63_sequencia,"","y68_seq"));
    if($cllevvalorpgtos->numrows>0){

      $cllevvalorpgtos->excluir($y63_sequencia);
      $erro_msg=$cllevvalorpgtos->erro_msg;
      if($cllevvalorpgtos->erro_status==0){
        $sqlerro=true;
      }
      if( $sqlerro == false){
        $clvalordefla->excluir($y63_sequencia);
        $erro_msg = $clvalordefla->erro_msg;
        if($clvalordefla->erro_status==0){
          db_msgbox($erro_msg);
          $sqlerro = true;
        }
      }
    }
  }

  //rotina para que caso já tenha registro na tabela  apagar esses registros
  if(!$sqlerro){

    $result = $cllevantanotas->sql_record($cllevantanotas->sql_query_file(null,"*","","y79_sequencia=$y63_sequencia"));
    if($cllevantanotas->numrows>0){

      $cllevantanotas->excluir("","y79_sequencia = $y63_sequencia");
      $erro_msg=$cllevantanotas->erro_msg;
      if($cllevantanotas->erro_status==0){
        $sqlerro=true;
      }
    }
  }

  if(!$sqlerro){

    $cllevvalor->excluir($y63_sequencia);
    $erro_msg=$cllevvalor->erro_msg;
    if($cllevvalor->erro_status==0){
      $sqlerro=true;
    }
  }
  if(!$sqlerro){
    $opcao='excluir';
  }
  db_fim_transacao($sqlerro);
  if(!$sqlerro){
    Header('Location: '.$_SERVER['PHP_SELF'].'?y60_contato=&y63_codlev='.$y63_codlev);
  }
}elseif(isset($opcao)){
  $notas   = '';
  $valores = '';
   //rotina para trazer os campos da tabela
   $result = $cllevvalor->sql_record($cllevvalor->sql_query_file("","fis_levvalor.*","","y63_sequencia=$y63_sequencia and  y63_codlev=$y63_codlev"));
   db_fieldsmemory($result,0);

   $apagar = ($y63_bruto*$y63_aliquota)/100;
   $result = $cllevvalorpgtos->sql_record("select * from fiscalizacao.fis_levvalor inner join fiscalizacao.fis_levvalorpgtos on y63_sequencia = y68_sequencia inner join fiscalizacao.fis_valordefla on yl63_sequencia = y68_sequencia and y68_seq = yl63_seq where y63_sequencia=$y63_sequencia and  y63_codlev=$y63_codlev");
   $numrows=$cllevvalorpgtos->numrows;
   $sSoma = 0;
   if($numrows>0){

     $valores = '';
     $vir     = '';
     for($r=0; $r<$numrows; $r++){
       db_fieldsmemory($result,$r);
       if($y68_pgto != ''){
        $valores.=$vir.$yl63_pagoriginal.'-'.$y68_pgto_dia.'/'.$y68_pgto_mes.'/'.$y68_pgto_ano;
        $sSoma += $yl63_pagoriginal;
      }else{
        $valores.=$vir.$yl63_pagoriginal.'-';
        $sSoma += $yl63_pagoriginal;
      }

      $vir="HHH";
     }
   }
   $vlrcons = $y63_pago;
   $y63_pago = $sSoma;
   if($numrows > 1){
      unset($y68_pgto);
      unset($y68_valor);
      unset($yl63_pagoriginal);
      unset($y68_pgto_dia);
      unset($y68_pgto_mes);
      unset($y68_pgto_ano);
   }

   //traz os dados da tabela levantanotas
   $result = $cllevantanotas->sql_record($cllevantanotas->sql_query_file(null,"*","y79_ordem","y79_sequencia=$y63_sequencia"));
   $numrows= $cllevantanotas->numrows;
   if($numrows>0){

     $notas='';
     $vir='';
     for($r=0; $r<$numrows; $r++){

       db_fieldsmemory($result,$r);
       $notas .= $vir.$y79_documento.'_sep_'.$y79_valor.'_sep_'.$y79_data_dia.'/'.$y79_data_mes.'/'.$y79_data_ano.'_sep_'.$y79_ordem;
       $vir="HHH";
     }
   }
   $db_botao = true;
}

if(isset($db_opcaoal)){

  $db_opcao=33;
  $db_botao=false;
}else if(isset($opcao) && $opcao=="alterar"){

  $db_opcao = 2;
  $db_botao=true;
}elseif(isset($opcao) && $opcao=="excluir" || isset($db_opcao) && $db_opcao==3){

  $db_opcao = 3;
  $db_botao=true;
}else{

  $db_opcao = 1;
  $db_botao=true;
}

if((isset($novo) && $novo=="ok")|| (isset($sqlerro) && $sqlerro==false)){
    $y63_sequencia='';
    $rsData = db_query("select y60_dtfim as dtfim from fiscalizacao.fis_levanta where y60_codlev = ".$y63_codlev."            AND  extract(year from y60_dtfim)  = {$y63_ano}
         AND  extract(month from y60_dtfim) = {$y63_mes} ");
    if($rsData && pg_num_rows($rsData) == 0){
    // die((int)$y63_mes."-".(int)$dtfimMes."-".$y63_ano."-".$dtfimAno);

      $y63_mes++;
      if($y63_dtvenc_mes<12){

        $y63_dtvenc_mes++;
        $y63_dtvenc_mes= (strlen($y63_dtvenc_mes)==1?"0$y63_dtvenc_mes":"$y63_dtvenc_mes");
      }else{

        $y63_dtvenc_mes='01';
        $y63_dtvenc_ano++;
      }

    if($y63_mes > 12 && $db_opcao == 1){
     $y63_ano++;
    }
  }

  $y63_bruto    = '';
  // $y63_aliquota = '';
  $y63_pago     = '';
  $y68_pgto     = '';
  $y68_pgto_dia = '';
  $y68_pgto_mes = '';
  $y68_pgto_ano = '';
  $y63_saldo    = '';
  $y63_histor   = '';
  $valores      = '';
  $notas        = '';
  $apagar       = '';
  $vlrcons      = '';
  unset($novo);
}

if (!empty($y63_mes) && !empty($y63_ano)) {
  $aMes = $y63_mes;
  $aAno = $y63_ano;
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
<body onLoad="document.form1.y63_bruto.focus();" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
  <?php
  include modification("forms/db_frm_fis_levvalor.php");
  ?>
    </center>
  </td>
  </tr>
</table>
</body>
</html>
<?php
if(isset($incluir) || isset($alterar) || isset($excluir)){

  if($cllevvalor->erro_status=="0"){

    db_msgbox($erro_msg);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllevvalor->erro_campo!=""){

      echo "<script> document.form1.".$cllevvalor->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllevvalor->erro_campo.".focus();</script>";
    }
  }
}

function Deflacionar_valores( $dtVencimento , $dtPagamento , $dValor , $sIntit ){

  $vCorrigido    = 0; // Valor Corrigido
  $vDescorrigido = $dValor; // Valor Descorrigido

  /* --[       Verifica a receita cadastrada nos parametros do Fiscal    ]--  */
  /* --[      buscando na tabrec para buscar o codigo de inflaçao        ]--  */

  $rsGetReceita = db_query("  SELECT k02_corr
                                  FROM fiscalizacao.fis_parfiscal
                                    INNER JOIN tabrec   ON tabrec.k02_codigo  = fis_parfiscal.y32_receit
                                    INNER JOIN tabrecjm ON tabrecjm.k02_codjm = tabrec.k02_codjm
                              WHERE fis_parfiscal.y32_instit = {$sIntit}                                 ");

  $cInfla = db_utils::fieldsMemory($rsGetReceita, 0)->k02_corr;

  /* -- [Pega Ano do Vencimento e o Ano do Pagamento] -- */
  $sAnoVenc = substr( $dtVencimento , 6 , 10 );
  $sAnoPag  = substr( $dtPagamento  , 6 , 10 );
  /* -- [Pega Mes do Vencimento e o Mes do Pagamento] -- */
  $sMesVenc = substr( $dtVencimento , 3 , 2 );
  $sMesPag  = substr( $dtPagamento  , 3 , 2 );

  /* -- [ Verifica sempre o mes anterior da competencia ] -- */
  $sMesPag = ( $sMesPag - 1 );
  if( $sMesPag == 0){
    $sMesPag = 12;
    $sAnoPag = ( $sAnoPag - 1 );
  }
/*  $sMesVenc = ( $sMesVenc + 1 );
  if( $sMesVenc == 13){
    $sMesVenc = 1;
    $sAnoVenc = ( $sAnoVenc + 1 );
  }
  */
  /* -- [ Executa Loop Entre os anos em ordem Descrescente ] -- */
  $cValor = 0;
  for ( $iAno = (int)$sAnoPag ; $iAno >= (int)$sAnoVenc ; $iAno-- ) {

    /* -- [ Logica para fazer loop do Mes ] -- */
    if ( $iAno == $sAnoVenc ){
      $iMesFim = $sMesVenc;
    }else{
      $iMesFim = 1;
    }

    if( $sAnoPag != $iAno ){
      $iMesIni = 12;
    }else{
      $iMesIni = $sMesPag;
    }
     /*-- [Executa Loop Entre os Meses em ordem Descrescente] --*/
    for ( $i = (int)$iMesIni; $i >= (int)$iMesFim ; $i-- ) {
        /*-- [Verificar o valor de inflaçao no mes decorrente] --*/
        $sSqlInflator  = "    SELECT i02_valor                                            ";
        $sSqlInflator .= "           FROM infla                                           ";
        $sSqlInflator .= "           WHERE i02_codigo = '{$cInfla}'                       ";
        $sSqlInflator .= "              AND  extract(year from infla.i02_data)  = {$iAno} ";
        $sSqlInflator .= "              AND  extract(month from infla.i02_data) = {$i}    ";
        $rsInflator = db_query( $sSqlInflator );
        $cValor     = db_utils::fieldsMemory($rsInflator, 0)->i02_valor;
        $vDescorrigido = ($vDescorrigido/$cValor);

    }
  }
  /* ---[ Faz a deflaçao do valor de acordo com a competencia ]--- */
  return round($vDescorrigido,2); // Retorna o valor deflacionado

}
?>
