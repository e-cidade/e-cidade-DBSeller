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
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
</script>
</head>
<body>

<?php 
  if ($oLinhas > 0) {  // verifica se a matricula passada como parametro encontrou registro no sql acima
    db_fieldsmemory($oResulQuery,0,true);
    db_fieldsmemory($oResulQuery2,0,true);
?>

<table  border="0" align="center" cellpadding="0" cellspacing="2">
  <tr bgcolor="#CCCCCC">
    <td colspan="4" align="center"><font color="#333333"><strong><b>&nbsp;DADOS DA NOTIFICAÇÃO DE LANÇAMENTO </b></strong></font><font color="#666666"><strong>
      </strong></font></td>
  </tr>
  <tr>
    <td width="100" align="right" nowrap bgcolor="#CCCCCC">&nbsp;NOTIFICAÇÃO DE LANÇ.  n&ordm;:&nbsp;</td>
    <td width="165" align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
      <?=$nl01_codlanc?>
      &nbsp; </strong></font></td>
    <td width="100"  align="right" nowrap bgcolor="#CCCCCC">&nbsp;N° DO BLOCO: </td>
    <td width="165" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;<?=$nl01_numbloco?>
      </strong></font></td>
  </tr>
  <?php 



  $oResulQuery_busca= $cllanc->sql_record($cllanc->sql_query_busca($codlanc));
  if ($cllanc->numrows>0){
    db_fieldsmemory($oResulQuery_busca,0);
  }
  ?>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;IDENTIFICAÇÃO:&nbsp; </td>
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
      <?=@substr($nl01_obs,0,25)?>
      &nbsp; </strong></font></td>


  </tr>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Departamento:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
      <?=@$coddepto."-".@$descrdepto?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Nome da Pessoa Autuada:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
      <?=@$nl01_nome?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Data de ciência:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
      <?=$data_ciencia?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Hora:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
      <?=@$y39_hora?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Vencimento Atual:&nbsp;</td>
    <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
      <?=@$nl01_dtvenc?>
      &nbsp; </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Prazo p/ Recurso:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
      <?=@$nl01_prazorec?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
     <td align="center" nowrap bgcolor="#CCCCCC" colspan=4 ><b>Endereço Registrado:&nbsp;</b></td>
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
    <td align="right" nowrap bgcolor="#CCCCCC">Rua:&nbsp; </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
      <?=@$j14_nome?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">n&deg; : </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
      <?=@$nl02_numero?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
      <?=@$j13_descr?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
      <?=@$nl02_compl?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
    <td align="center" nowrap bgcolor="#CCCCCC" colspan=4 ><b>Endereço Localizado:&nbsp;</b></td>
  </tr>
  <?php 
  $oResulQuery_exec = $cllancexec->sql_record($cllancexec->sql_query($codlanc));

  if ($cllancexec->numrows>0){
    db_fieldsmemory($oResulQuery_exec,0,true);
  }
  ?>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">Rua:&nbsp; </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
      <?=@$j14_nome?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">n&deg; : </td>
    <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
      <?=@$nl03_numero?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
    <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
      <?=@$j13_descr?>
      </strong></font></td>
    <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
    <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
      <?=@$nl03_compl?>
      &nbsp; </strong></font></td>
  </tr>
  <tr>
    <td colspan="4" align="left"><table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr >
            <td >
      <table  border="0" cellspacing="2" cellpadding="0">
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" ><a href="fis3_fis_conslanc002_detalhes.php?solicitacao=Proced&codlanc=<?=$codlanc?>" target="iframeDetalhes">&nbsp;Procedências&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" ><a href="fis3_fis_conslanc002_detalhes.php?solicitacao=Receita&codlanc=<?=$codlanc?>" target="iframeDetalhes">&nbsp;Receitas&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_conslanc002_detalhes.php?codlanc=<?=$codlanc?>&solicitacao=Fiscais" target="iframeDetalhes">&nbsp;Fiscais&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_conslanc002_detalhes.php?codlanc=<?=$codlanc?>&solicitacao=Testemunha" target="iframeDetalhes">&nbsp;Testemunha&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_conslanc002_detalhes.php?codlanc=<?=$codlanc?>&solicitacao=Responsavel" target="iframeDetalhes">&nbsp;Respons&aacute;vel&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="fis3_fis_conslanc002_detalhes.php?codlanc=<?=$codlanc?>&solicitacao=Calculo" target="iframeDetalhes">&nbsp;C&aacute;lculo&nbsp;</a></td>
              </tr>
              <tr>
                <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                  <a href="fis3_fis_conslanc002_detalhes.php?codlanc=<?=$codlanc?>&solicitacao=Andamentos" target="iframeDetalhes">Andamentos</a>
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
    <td align="center"><strong>Pesquisa Notificaçao de Lançamento
      n&atilde;o retornou nenhum registro.</strong></td>
  </tr>
</table>
<?php 
  } // fim da verificacao
?>
</body>
</html>
