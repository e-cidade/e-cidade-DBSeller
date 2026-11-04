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
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clauto = new cl_fis_auto;
$clautolocal = new cl_fis_autolocal;
$clautoexec = new cl_fis_autoexec;
// Query add em 08-06-2016
$sql  = "SELECT fis_auto.*, db_depart.*, fis_tipofiscaliza.*, cgm.*, fis_datacienciaandamento.* FROM fiscalizacao.fis_auto ";
$sql .= "INNER JOIN db_config ON db_config.codigo = fis_auto.y50_instit ";
$sql .= "INNER JOIN db_depart ON db_depart.coddepto = fis_auto.y50_setor ";
$sql .= "INNER JOIN fiscalizacao.fis_tipofiscaliza ON fis_tipofiscaliza.y27_codtipo = fis_auto.y50_codtipo ";
$sql .= "INNER JOIN cgm ON  cgm.z01_numcgm = db_config.numcgm ";
$sql .= "LEFT JOIN fiscalizacao.fis_procfiscalauto ON y111_auto = y50_codauto ";
$sql .= "LEFT JOIN fiscalizacao.fis_procfiscal ON y111_procfiscal = y100_sequencial ";
$sql .= "LEFT JOIN fiscalizacao.fis_autoandam ON y58_codauto = y50_codauto ";
$sql .= "LEFT JOIN fiscalizacao.fis_fandam ON y58_codandam = y39_codandam ";
$sql .= "LEFT JOIN fiscalizacao.fis_datacienciaandamento ON fis_datacienciaandamento.fandam = y39_codandam ";
$sql .= "LEFT JOIN fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo ";
$sql .= "LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON fi30_tipoandam = y41_codtipo ";
$sql .= "LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial ";
$sql .= "WHERE y50_codauto = $codauto and y50_instit = ".db_getsession('DB_instit');
$sql .= " ";
$result = db_query($sql);
//$result = $clauto->sql_record($clauto->sql_query($codauto,"*",null," y50_codauto = $codauto and y50_instit = ".db_getsession('DB_instit') ));
$num = pg_num_rows($result);

$sql2 = "select data_ciencia from fiscalizacao.fis_autoandam
LEFT JOIN fiscalizacao.fis_fandam ON y58_codandam = y39_codandam
LEFT JOIN fiscalizacao.fis_datacienciaandamento ON fis_datacienciaandamento.fandam = y39_codandam where y58_codauto = $codauto and data_ciencia is not null";
$result2 = db_query($sql2);


?>

<html>
<head> 
<title>Dados da Inscri&ccedil;&atilde;o - BCI</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
</script>
</head>
<body>

<?php 
  if ($num > 0) {  // verifica se a matricula passada como parametro encontrou registro no sql acima
    db_fieldsmemory($result,0,true);
    db_fieldsmemory($result2,0,true);
?>

<table  border="0" align="center" cellpadding="0" cellspacing="2">
  <tr bgcolor="#CCCCCC"> 
    <td colspan="4" align="center"><font color="#333333"><strong><b>&nbsp;DADOS DO 
      AUTO DE INFRA&Ccedil;&Atilde;O&nbsp;</b></strong></font><font color="#666666"><strong> 
      </strong></font></td>
  </tr>
  <tr> 
    <td width="100" align="right" nowrap bgcolor="#CCCCCC">&nbsp;AUTO n&ordm;:&nbsp;</td>
    <td width="165" align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=$y50_codauto?>
      &nbsp; </strong></font></td>
    <td width="100"  align="right" nowrap bgcolor="#CCCCCC">&nbsp;N° DO BLOCO: </td>
    <td width="165" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;<?=$y50_numbloco?>
      </strong></font></td>
  </tr>
  <?php 
  $result_busca= $clauto->sql_record($clauto->sql_query_busca($codauto));
  if ($clauto->numrows>0){
    db_fieldsmemory($result_busca,0);
  }
  ?>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;IDENTIFICAÇÃ0:&nbsp; </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=@$dl_identificacao?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">C&oacute;digo Ident.:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp; 
      <?=@$dl_codigo?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Tipo de Fiscalização:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@$tipo?>
      &nbsp; </strong></font></td>


    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Obs:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@substr($y50_obs,0,25)?>
      &nbsp; </strong></font></td>


  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Departamento:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@$coddepto."-".@$descrdepto?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Nome da Pessoa Autuada:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@$y50_nome?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Data de ciência:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=$data_ciencia?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Hora:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@$y50_hora?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Vencimento Atual:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@$y50_dtvenc?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Prazo p/ Recurso:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong> 
      <?=@$y50_prazorec?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
     <td align="center" nowrap bgcolor="#CCCCCC" colspan=4 ><b>Endereço Registrado:&nbsp;</b></td>
  </tr>
  <?php 
  $result_local=$clautolocal->sql_record($clautolocal->sql_query($codauto));
  if ($clautolocal->numrows>0){
    db_fieldsmemory($result_local,0,true);
  }else{
    $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y14_numero,end01_compl as  y14_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$codauto} and end01_tipopeca = 'A' " );
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
      <?=@$y14_numero?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$j13_descr?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$y14_compl?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="center" nowrap bgcolor="#CCCCCC" colspan=4 ><b>Endereço Localizado:&nbsp;</b></td>
  </tr>
  <?php 
  $result_exec=$clautoexec->sql_record($clautoexec->sql_query($codauto));
  if ($clautoexec->numrows>0){
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
      <?=@$y15_numero?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$j13_descr?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp; 
      <?=@$y15_compl?>
      &nbsp; </strong></font></td>
  </tr>
  <tr> 
    <td colspan="4" align="left"><table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr > 
            <td >
	    <table  border="0" cellspacing="2" cellpadding="0">
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" ><a href="fis3_fis_consauto002_detalhes.php?solicitacao=Proced&auto=<?=$codauto?>" target="iframeDetalhes">&nbsp;Procedências&nbsp;</a></td>
              </tr>
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" ><a href="fis3_fis_consauto002_detalhes.php?solicitacao=Receita&auto=<?=$codauto?>" target="iframeDetalhes">&nbsp;Receitas&nbsp;</a></td>
              </tr> 
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consauto002_detalhes.php?auto=<?=$codauto?>&solicitacao=Fiscais" target="iframeDetalhes">&nbsp;Fiscais&nbsp;</a></td>
              </tr>
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consauto002_detalhes.php?auto=<?=$codauto?>&solicitacao=Testemunha" target="iframeDetalhes">&nbsp;Testemunha&nbsp;</a></td>
              </tr>
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consauto002_detalhes.php?auto=<?=$codauto?>&solicitacao=Responsavel" target="iframeDetalhes">&nbsp;Respons&aacute;vel&nbsp;</a></td>
              </tr>
              <tr> 
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_consauto002_detalhes.php?auto=<?=$codauto?>&solicitacao=Calculo" target="iframeDetalhes">&nbsp;C&aacute;lculo&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                  <a href="fis3_fis_consauto002_detalhes.php?auto=<?=$codauto?>&solicitacao=Andamentos" target="iframeDetalhes">Andamentos</a>
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
    <td align="center"><strong>Pesquisa do Auto n&deg;
      &nbsp;<?php //=$numeroDaInscricao?>&nbsp;
      n&atilde;o retornou nenhum registro.</strong></td>
  </tr>
</table>
<?php  
  } // fim da verificacao
?>
</body>
</html>
