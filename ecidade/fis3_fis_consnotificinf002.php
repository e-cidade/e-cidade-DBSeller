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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_fis_fiscal_classe.php"));
include(modification("classes/db_fis_fiscalocal_classe.php"));
include(modification("classes/db_fis_fiscexec_classe.php"));
include(modification("classes/db_fis_fiscalbaixa_classe.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
// -------------

if( isset( $intimacao ) && $intimacao == 1 ){
            $whereIntimacao = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = true) ';
}else{
        $whereIntimacao = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = false) ';
}

$where2 .= ' and (case when (fis_grupotipoandamento.sequencial = 4 or fis_grupotipoandamento.sequencial = 2) then ' ;
$where2 .= '  fis_processofiscalativo.fiscal = '.db_getsession('DB_id_usuario');
$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE) ";
$where2 .= " and fis_processofiscalativo.ativo = 't' ";
$where2 .= " when  y30_codnoti not in (select y49_codnoti from fiscalizacao.fis_fiscalandam) then";
$where2 .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE)";
$where2 .= " and fis_processofiscalativo.ativo = 't'";
$where2 .= " else 1=1 end ) ";

$tipoEnd     = ((isset($intimacao) && $intimacao == 1) ? 'I' : 'N');
$clfiscal = new cl_fis_fiscal;
$clfiscalocal = new cl_fis_fiscalocal;
$clfiscexec = new cl_fis_fiscexec;
$clfiscalbaixa = new cl_fis_fiscalbaixa;
$sql  = "select * from fiscalizacao.fis_fiscal ";
$sql .= "inner join db_depart on db_depart.coddepto = fis_fiscal.y30_setor ";
$sql .= "inner join db_config on db_config.codigo = db_depart.instit ";
$sql .= "left join fiscalizacao.fis_procfiscalnotificacao on y30_codnoti = y110_notificacaofiscal ";
$sql .= "left join fiscalizacao.fis_procfiscal on y110_procfiscal = y100_sequencial ";
$sql .= "left join fiscalizacao.fis_fiscalandam on y30_codnoti = y49_codnoti ";
$sql .= "left join fiscalizacao.fis_fandam on y49_codandam = y39_codandam ";
$sql .= "left join fiscalizacao.fis_datacienciaandamento on fis_datacienciaandamento.fandam = y39_codandam ";
$sql .= "left join fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo ";
$sql .= "left join fiscalizacao.fis_grupotipoandamento_tipoandam ON fi30_tipoandam = y41_codtipo ";
$sql .= "left join fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial ";
// $sql .= "
// left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti
//             left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial
// left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario
// left join db_usuarios on db_usuarios.id_usuario = y38_id_usuario
// left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial
//             left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario ";
$sql .= "where y30_codnoti = $codfiscal $whereIntimacao ";
//$result = $clfiscal->sql_record($clfiscal->sql_query($codfiscal,"*",null," y30_codnoti = $codfiscal and y30_instit = ".db_getsession('DB_instit')));
// die($sql);
$result = $clfiscal->sql_record($sql);
$num = pg_numrows($result);


$sql2  = "select data_ciencia,y39_hora as y30_hora from fiscalizacao.fis_fiscalandam left join fiscalizacao.fis_fandam on y49_codandam = y39_codandam ";
$sql2 .= "left join fiscalizacao.fis_datacienciaandamento on fis_datacienciaandamento.fandam = y39_codandam where y49_codnoti =$codfiscal and data_ciencia is not null";
// echo $sql2;
$result2 = $clfiscal->sql_record($sql2);
$num2 = pg_numrows($result2);
?>

<html>
<head> 
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
</script>
</head>
<body>

<?php 
  if ($num > 0) { 
    db_fieldsmemory($result,0,true);
    if($num2 > 0)
    db_fieldsmemory($result2,0);
    // echo $data_ciencia;
?>

<table  border="0" align="center" cellpadding="0" cellspacing="2">
  <tr bgcolor="#CCCCCC"> 
    <td colspan="4" align="center"><font color="#333333"><strong><b>&nbsp;DADOS DA 
      <?=mb_strtoupper($getLabel)?></b></strong></font><font color="#666666"><strong> 
      </strong></font></td>
  </tr>
  <tr> 
    <td width="100" align="right" nowrap bgcolor="#CCCCCC">&nbsp;<?=mb_strtoupper($getLabel)?> n&ordm;:&nbsp;</td>
    <td width="165" align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=$y30_codnoti?>
      &nbsp; </strong></font></td>
    <td width="100"  align="right" nowrap bgcolor="#CCCCCC">&nbsp;N° DO BLOCO: </td>
    <td width="165" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;<?=$y30_numbloco?>
      </strong></font></td>
  </tr>
  <?php 
  
  $result_busca= $clfiscal->sql_record($clfiscal->sql_query_busca($codfiscal," dl_noti = $codfiscal and y30_instit = ".db_getsession('DB_instit') ) );
  if ($clfiscal->numrows>0){
    db_fieldsmemory($result_busca,0);
  }
  ?>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;IDENTIFICAÇÃ0:&nbsp; </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=$dl_identifica?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">C&oacute;digo Ident.:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=$dl_codigo?>
      &nbsp; </strong></font></td>
  </tr>
  
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;        &nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?php 
           
      ?>
      &nbsp; </strong></font></td>


    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Obs:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=substr($y30_obs,0,25)?>
      &nbsp; </strong></font></td>


  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Departamento:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=$coddepto."-".$descrdepto?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Nome da Pessoa Autuada:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=$y30_nome?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Data de ciência:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=db_formatar($data_ciencia,'d')?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Hora:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=$y30_hora?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Vencimento Atual:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=$y30_dtvenc?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Prazo p/ Recurso:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=$y30_prazorec?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
     <td align="center" nowrap bgcolor="#CCCCCC" colspan=4 ><b>Endereço Registrado:&nbsp;</b></td>
  </tr>
  <?php 
  $result_local=$clfiscalocal->sql_record($clfiscalocal->sql_query($codfiscal));
  if ($clfiscalocal->numrows>0){
    db_fieldsmemory($result_local,0,true);
  }else{
    $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y12_numero,end01_compl as  y12_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$codfiscal} and end01_tipopeca = '$tipoEnd' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
  }
  ?>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">Rua:&nbsp; </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=@$j14_nome?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">n&deg; : </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=@$y12_numero?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$j13_descr?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$y12_compl?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="center" nowrap bgcolor="#CCCCCC" colspan=4 ><b>Endereço Localizado:&nbsp;</b></td>
  </tr>
  <?php 
  $result_exec=$clfiscexec->sql_record($clfiscexec->sql_query($codfiscal));
  if ($clfiscexec->numrows>0){
    db_fieldsmemory($result_exec,0,true);
  }
  ?>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">Rua:&nbsp; </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=@$j14_nome?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">n&deg; : </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=@$y13_numero?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$j13_descr?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$y13_compl?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td colspan="4" align="left"><table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr > 
            <td >
	    <table  border="0" cellspacing="2" cellpadding="0">
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" ><a href="fis3_fis_consnotific002_detalhes.php?solicitacao=Proced&fiscal=<?=$codfiscal?><?=$getIntimacao?>" target="iframeDetalhes">&nbsp;Procedências&nbsp;</a></td>
              </tr> 
              <!--<tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" ><a href="fis3_fis_consnotific002_detalhes.php?solicitacao=Receita&fiscal=<?=$codfiscal?><?=$getIntimacao?>" target="iframeDetalhes">&nbsp;Receitas&nbsp;</a></td>
              </tr>
              --> 
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consnotific002_detalhes.php?fiscal=<?=$codfiscal?>&solicitacao=Fiscais<?=$getIntimacao?>" target="iframeDetalhes">&nbsp;Fiscais&nbsp;</a></td>
              </tr>
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consnotific002_detalhes.php?fiscal=<?=$codfiscal?>&solicitacao=Testemunha<?=$getIntimacao?>" target="iframeDetalhes">&nbsp;Testemunha&nbsp;</a></td>
              </tr>
              <!--
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consnotific002_detalhes.php?fiscal=<?=$codfiscal?>&solicitacao=Calculo<?=$getIntimacao?>" target="iframeDetalhes">&nbsp;C&aacute;lculo&nbsp;</a></td>
              </tr>
              -->
              <?php 
              $result_baixa=$clfiscalbaixa->sql_record($clfiscalbaixa->sql_query_file(null,"*",null,"y47_codnoti = $codfiscal"));
              if ($clfiscalbaixa->numrows>0){
              ?>
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consnotific002_detalhes.php?fiscal=<?=$codfiscal?>&solicitacao=Baixa<?=$getIntimacao?>" target="iframeDetalhes">&nbsp;Fiscais&nbsp;</a></td>
              </tr>
              <?php 
              }
              ?>
              <tr>
              	<td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
              		<a href="fis3_fis_consnotific002_detalhes.php?fiscal=<?=$codfiscal?>&solicitacao=Andamentos<?=$getIntimacao?>" target="iframeDetalhes">Andamentos</a>
              	</td>
              </tr>
            </table>
          <td width="88%" align="left"> <iframe align="middle" width="100%"  frameborder="0" marginheight="0" marginwidth="0" name="iframeDetalhes" > 
            </iframe> </td>
        </tr>
      </table></td>
  </tr>
</table>

<?php 
  } else {  // caso nao tenha retornado nenhum registro é mostrado uma tabela informando que a matricula nao foi localizada
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td align="center"><strong>Pesquisa da Notificação n&deg;
      &nbsp;<?php //=$numeroDaInscricao?>&nbsp;
      n&atilde;o retornou nenhum registro.</strong></td>
  </tr>
</table>
<?php  
  } // fim da verificacao
?>
</body>
</html>
