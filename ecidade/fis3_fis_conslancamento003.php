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
include(modification("classes/db_fis_auto_classe.php"));
include(modification("classes/db_fis_autolocal_classe.php"));
include(modification("classes/db_fis_autoexec_classe.php"));

require_once modification('classes/db_fis_lancamento_classe.php');
require_once modification('classes/db_fis_lancexec_classe.php');
require_once modification('classes/db_fis_lanclocal_classe.php');
$cllancamento = new cl_fis_lancamento;

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cllanc = new cl_fis_lancamento;

/*$clautolocal = new cl_fis_autolocal;*/
$clautoexec  = new cl_fis_autoexec;
$cllancexec  = new cl_fis_lancexec;
$cllanclocal = new cl_fis_lanclocal;

$sWhere      = "nl01_codlanc = $codlanc and nl01_instit = ".db_getsession('DB_instit');
$sCampos     = "  fis_lancamento.*, db_depart.*, fis_tipofiscaliza.*, cgm.*, fis_datacienciaandamento.* ";
$oQueryLanc  = $cllancamento->sql_query_consulta( null, $sCampos, null, $sWhere );
$oResulQuery = db_query($oQueryLanc);
$oLinhas     = pg_num_rows($oResulQuery);

$sql2 = "select data_ciencia, y39_hora from fiscalizacao.fis_lancandam
LEFT JOIN fiscalizacao.fis_fandam ON nl19_codandam = y39_codandam
LEFT JOIN fiscalizacao.fis_datacienciaandamento ON fis_datacienciaandamento.fandam = y39_codandam where nl19_codlanc = $codlanc and data_ciencia is not null";
$oResulQuery2 = db_query($sql2);


?>

<html>
<head>
<title>Dados da Inscri&ccedil;&atilde;o - BCI</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
  // var codauto = <?=$codlanc?>;
  function js_imprime(codlanc) {

    var sLocation  = "fis3_fis_consulancamento_imp.php?";
  	sLocation     += "codlanc="+codlanc;
  	jan            = window.open(sLocation, '', 
  	  	             'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
    jan.moveTo(0,0);
   }
</script>


</head>
<body>

<?php
  if ($oLinhas > 0) {  // verifica se a matricula passada como parametro encontrou registro no sql acima
    db_fieldsmemory($oResulQuery,0,true);
    db_fieldsmemory($oResulQuery2,0,true);
?>

<table  border="0" align="center">

  <tr bgcolor="#CCCCCC">
    <td colspan="4" style="text-align: center;">
      <label><h2>DADOS DA NOTIFICAÇÃO DE LANÇAMENTO</h2></label>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;NOTIFICAÇÃO DE LANÇ. nº </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=$nl01_codlanc?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;N° DO BLOCO </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=$nl01_numbloco?>&nbsp; </strong>
    </td>
  </tr>
  <?php

  $oResulQuery_busca= $cllanc->sql_record($cllanc->sql_query_busca($codlanc));
  if ($cllanc->numrows>0){
    db_fieldsmemory($oResulQuery_busca,0);
  }

  ?>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;IDENTIFICAÇÃO </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$dl_identificacao?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Código Ident. </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$dl_codigo?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Tipo de Fiscalização </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$tipo?>&nbsp; </strong>
    </td>


    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Obs </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@substr($nl01_obs,0,25)?>&nbsp; </strong>
    </td>

  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Departamento </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$coddepto."-".@$descrdepto?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Nome da Pessoa Autuada </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl01_nome?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Data de ciência </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=$data_ciencia?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Hora </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$y39_hora?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Vencimento Atual </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl01_dtvenc?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Prazo p/ Recurso </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl01_prazorec?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
     <td align="center" nowrap bgcolor="#CCCCCC" colspan="4" ><h3 style="margin-top: 6px;">Endereço Registrado&nbsp;</h3></td>
  </tr>
  <?php
  $oResulQuery_local=$cllanclocal->sql_record($cllanclocal->sql_query($codlanc));
  if ($cllanclocal->numrows>0){
    db_fieldsmemory($oResulQuery_local,0,true);
  }else{
    $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as nl02_numero,end01_compl as  nl02_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$codlanc} and end01_tipopeca = 'L' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
  }
  ?>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Rua </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$j14_nome?></strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;nº </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl02_numero?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Bairro </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$j13_descr?></strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Complemento </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl02_compl?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
    <td align="center" nowrap bgcolor="#CCCCCC" colspan="4" ><h3 style="margin-top: 6px;">Endereço Localizado&nbsp;</h3></td>
  </tr>
  <?php
  $oResulQuery_exec = $cllancexec->sql_record($cllancexec->sql_query($codlanc));

  if ($cllancexec->numrows>0){
    db_fieldsmemory($oResulQuery_exec,0,true);
  }
  ?>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Rua </td>
    <td nowrap bgcolor="#FFFFFF"> <font color="#666666">
      <strong style="color: #666666;">&nbsp;<?=@$j14_nome?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;nº </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl03_numero?>&nbsp; </strong></td>
  </tr>
  <tr>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Bairro </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$j13_descr?>&nbsp; </strong>
    </td>
    <td align="left" nowrap bgcolor="#CCCCCC">&nbsp;Complemento </td>
    <td nowrap bgcolor="#FFFFFF">
      <strong style="color: #666666;">&nbsp;<?=@$nl03_compl?>&nbsp; </strong>
    </td>
  </tr>
  <tr>
    <td colspan="4" align="left"><table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr >
            <td >
<?php

   $sqllanc = "select distinct nl01_codlanc,
                  nl01_data,
                  pl09_paragrafo,
                  pl09_descr,
                  pl30_texto from fiscalizacao.fis_lancamento 
      left join fiscalizacao.fis_paragrafolanc on nl01_codlanc = pl30_codlanc 
      left join fiscalizacao.fis_paragrafo on pl30_paragrafo = pl09_paragrafo and nl01_setor = pl09_coddepto 
      where pl30_codlanc = {$codlanc} and pl09_status = true order by 3;";

  $resultlanc = db_query($sqllanc);
  $ln = pg_fetch_all($resultlanc);

?>

<table  align="center"  style="margin-top: 2%;">

    <?php foreach ($ln as $rln) {?>
      
      <tr> 
            <td><legend><b><?=$rln["pl09_descr"]?></b></td>
            <td><textarea style="width: 400px;" readonly><?=$rln["pl30_texto"]; ?></textarea></td>
        
      </tr>

    <?php } ?>
</table>
<div style="text-align:center;">
  
  <input  style="margin-top: 30px;" id='imprimir' type="button" value='Imprimir' onclick="js_imprime(<?=$codlanc?>);"/>

  <input  style="margin-top: 30px;" type="button" value='Fechar'  onclick='parent.Jandb_iframe_AutoNotif.hide()'/>
  
</div>
<?php
  } else {  // caso nao tenha retornado nenhum registro é mostrado uma tabela informando que a matricula nao foi localizada
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><strong>Pesquisa Notificaçao de Lançamento
      n&atilde;o retornou nenhum registro.</strong></td>
  </tr>
</table>
<?php
  } // fim da verificacao
?>
</body>
</html>
