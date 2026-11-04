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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_arrematric_classe.php"));
require_once(modification("classes/db_arreinscr_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$oPost = db_utils::postMemory($HTTP_POST_VARS);

switch ($oPost->tipo) {

	case 1:
		$oClasseVinculo = new cl_arrematric();
		$sCampo         = "k00_matric";
		$sLabel         = "Matricula";

		$rsPencentualBloqueado = db_query("select coalesce(sum(k00_perc),0) as sum from arreinscr where k00_numpre = {$numpre}");
		$nPercentualBloqueado  = db_utils::fieldsMemory($rsPencentualBloqueado, 0)->sum;
	break;

	case 2:
		$oClasseVinculo  = new cl_arreinscr();
		$sCampo          = "k00_inscr";
		$sLabel          = "Inscrição";

		$rsPencentualBloqueado = db_query("select coalesce(sum(k00_perc),0) as sum from arrematric where k00_numpre = {$numpre}");
		$nPercentualBloqueado  = db_utils::fieldsMemory($rsPencentualBloqueado, 0)->sum;
	break;

}

$oClasseVinculo->rotulo->label();
$cliframe_alterar_excluir  = new cl_iframe_alterar_excluir;
$oDadosRecalculoPercentual = new stdClass;

$db_opcao  = 1;
$db_opcao2 = 1;
$db_botao  = true;
$lErro     = false;
$sErroMsg  = "Operação realizada com Sucesso";

$k00_numpre  = $oPost->numpre;

if (isset($operacao) ) {

	$oDadosRecalculoPercentual->iNumpre              = $k00_numpre;
	$oDadosRecalculoPercentual->sCampo               = $sCampo;
	$oDadosRecalculoPercentual->sCampoValor          = $$sCampo;
	$oDadosRecalculoPercentual->nPercentual          = $oPost->k00_perc;
	$oDadosRecalculoPercentual->nPercentualBloqueado = $nPercentualBloqueado;
	$oDadosRecalculoPercentual->sLabelCampo          = $sLabel;

	switch ($operacao) {

		case "incluir":

			try {

				db_inicio_transacao();

				$oClasseVinculo->k00_perc = $oPost->k00_perc;
				$oClasseVinculo->incluir($k00_numpre, $$sCampo);
				if ($oClasseVinculo->erro_status == "0"){
					throw new Exception("Erro excluindo dados\n".$oClasseVinculo->erro_msg);
				}

				$oRetornoRecalculoPercentual = recalculaPercentual($oDadosRecalculoPercentual);
				if ($oRetornoRecalculoPercentual->lErro) {
					throw new Exception($oRetornoRecalculoPercentual->sMsg);
				}

				$oRetornoArreHist = geraArreHist($k00_numpre, $oPost->obs, $sLabel);
				if ($oRetornoArreHist->lErro) {
					throw new Exception($oRetornoArreHist->sMsg);
				}

				if ($oPost->k00_perc == 100) {
					$nPercentualBloqueado = 0;
				}

				db_fim_transacao(false);

				$k00_perc = "";
				$$sCampo  = "";

			} catch (Exception $oErro) {

				db_fim_transacao(true);
				$lErro    = true;
				$sErroMsg = $oErro->getMessage();

			}

		break;

		case "alterar":

			try {

				db_inicio_transacao();

			  $oClasseVinculo->k00_perc = $oPost->k00_perc;
				$oClasseVinculo->alterar($k00_numpre, $$sCampo);
				if ($oClasseVinculo->erro_status == "0"){
					throw new Exception("Erro excluindo dados\n".$oClasseVinculo->erro_msg);
				}

				$oRetornoRecalculoPercentual = recalculaPercentual($oDadosRecalculoPercentual);
				if ($oRetornoRecalculoPercentual->lErro) {
					throw new Exception($oRetornoRecalculoPercentual->sMsg);
				}

				$oRetornoArreHist = geraArreHist($k00_numpre, $oPost->obs, $sLabel);
				if ($oRetornoArreHist->lErro) {
					throw new Exception($oRetornoArreHist->sMsg);
				}

				if ($oPost->k00_perc == 100) {
					$nPercentualBloqueado = 0;
				}

				db_fim_transacao(false);

				$k00_perc = "";
				$$sCampo  = "";

			} catch (Exception $oErro) {

				db_fim_transacao(true);
				$lErro    = true;
				$sErroMsg = $oErro->getMessage();

			}

		break;

		case "excluir":

			try {

			  db_inicio_transacao();

			  $oClasseVinculo->excluir($k00_numpre, $$sCampo);
			  if ($oClasseVinculo->erro_status == "0"){
			  	throw new Exception("Erro excluindo dados\n".$oClasseVinculo->erro_msg);
			  }

			  $oRetornoArreHist = geraArreHist($k00_numpre, $oPost->obs, $sLabel);
			  if ($oRetornoArreHist->lErro) {
			  	throw new Exception($oRetornoArreHist->sMsg);
			  }

			  db_fim_transacao(false);

			  $k00_perc = "";
			  $$sCampo  = "";

			} catch (Exception $oErro) {

				db_fim_transacao(true);
				$lErro    = true;
				$sErroMsg = $oErro->getMessage();

			}

		break;

		default:
			$rsDados=$oClasseVinculo->sql_record($oClasseVinculo->sql_query_file($k00_numpre, $$sCampo));
      db_fieldsmemory($rsDados, 0);
		break;

	}

}

if (isset($opcao)) {

	if ( $opcao == "excluir") {
		$db_opcao  = 3;
		$db_opcao2 = 3;
	}	else if ($opcao == "alterar") {
		$db_opcao  = 2;
		$db_opcao2 = 22;
	}
}

if (isset($novo) || $lErro) {
	$$sCampo     = "";
	$k00_perc    = "";
}

function recalculaPercentual($oDadosRecalculoPercentual) {

	$oRetorno = new stdClass;
	$oRetorno->lErro = false;
	$oRetorno->sMsg  = "";
	try {

	  $nPercentualTotal = 100-($oDadosRecalculoPercentual->nPercentual+$oDadosRecalculoPercentual->nPercentualBloqueado);

	  $sTabela1 = "arrematric";
	  $sTabela2 = "arreinscr";
	  if ($oDadosRecalculoPercentual->sCampo == "k00_inscr") {
	  	$sTabela1 = "arreinscr";
	  	$sTabela2 = "arrematric";
	  }

	  /*
	   * Caso o percentual cadastrado tenha sido 100% serão excluídos os demais registros ficando apenas o ultimo registro de 100%
	   * Caso contrario será recalculado o percentual dos demais registros com o que sobrou.
	   */
	  if ($oDadosRecalculoPercentual->nPercentual == 100) {

	  	if ($oDadosRecalculoPercentual->sCampo == "k00_inscr") {
	  		$sSql1 = "delete from arrematric
	  		                where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}";

	  	  $sSql2 = "delete from arreinscr
	  	                  where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}
	  	                    and {$oDadosRecalculoPercentual->sCampo} <> {$oDadosRecalculoPercentual->sCampoValor}";
	  	} else {
	  		$sSql1 = "delete from arreinscr
	  		                where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}";

	  		$sSql2 = "delete from arrematric
	  		                where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}
	  		                  and {$oDadosRecalculoPercentual->sCampo} <> {$oDadosRecalculoPercentual->sCampoValor}";
	  	}

	  	$rsQuery1 = db_query($sSql1);
	  	if (!$rsQuery1) {
	  		throw new Exception(pg_last_error());
	  	}

	  	$rsQuery2 = db_query($sSql2);
	  	if (!$rsQuery2) {
	  		throw new Exception(pg_last_error());
	  	}

	  } else {

	  	$sSqlPercentualCalculo = "select coalesce(round(($nPercentualTotal - sum(k00_perc))/count(*),2),0) as perc
	  	                            from $sTabela1
	  	                           where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}
	  	                             and {$oDadosRecalculoPercentual->sCampo} <> {$oDadosRecalculoPercentual->sCampoValor}";
	  	$rsPercentualCalculo   = db_query($sSqlPercentualCalculo);
	  	$nPerc = db_utils::fieldsMemory($rsPercentualCalculo, 0)->perc;
	  	if ($nPerc <> 0) {
	  	  $sSqlUpdate = "update $sTabela1 set k00_perc = k00_perc + {$nPerc}
	  	                  where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}
	  	                    and {$oDadosRecalculoPercentual->sCampo} <> {$oDadosRecalculoPercentual->sCampoValor}";
	  	  $rsUpdate   = db_query($sSqlUpdate);
	  	  if (!$rsUpdate) {
	  	  	throw new Exception(pg_last_error());
	  	  }
	  	}

	  	$sSqlValida = "select $oDadosRecalculoPercentual->sCampo as id
	  	                 from $sTabela1
	                    where k00_numpre = {$oDadosRecalculoPercentual->iNumpre}
	  	                  and k00_perc <= 0";
	  	$rsValida   = db_query($sSqlValida);
	  	if (pg_num_rows($rsValida) > 0) {
	  		$oDadosErro = db_utils::fieldsMemory($rsValida, 0);
	  		$sMsg = "\n".$oDadosRecalculoPercentual->sLabelCampo." ".$oDadosErro->id." com percentuais zerados ou negativos após o ajuste!\n";
	  		$sMsg .= "Informe outro valor de percentual";
	  		throw new Exception($sMsg);
	  	}

	  }

	} catch (Exception $oErro) {
		$oRetorno->lErro = true;
		$oRetorno->sMsg  = "Erro recalculando percentuais! ".$oErro->getMessage()." - ".pg_last_error();
	}

	return $oRetorno;

}

function geraArreHist($iNumpre, $sTexto, $sLabel) {

	$oRetorno = new stdClass;
	$oRetorno->lErro = false;
	$oRetorno->sMsg  = "";

	try {

	  $oArreHist = db_utils::getDao("arrehist");

	  $oArreHist->k00_numpre     = $iNumpre;
    $oArreHist->k00_numpar     = "0";
    $oArreHist->k00_hist       = "890";
    $oArreHist->k00_dtoper     = date("Y/m/d");
    $oArreHist->k00_hora       = date("h:i");
    $oArreHist->k00_id_usuario = db_getsession("DB_id_usuario");
    $oArreHist->k00_histtxt    = "Manutencao de vinculos de {$sLabel} com o numpre - ".$sTexto;
    $oArreHist->incluir(null);
    if ($oArreHist->erro_status == "0") {
    	throw new Exception( $oArreHist->erro_msg );
    }

	} catch (Exception $oErro) {
		throw new Exception ("Erro incluindo novo historico para o numpre!\n".$oErro->getMessage());
	}

	return $oRetorno;

}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">

<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>

<link href="estilos.css" rel="stylesheet" type="text/css">

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<br><br><br><br><br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
      <form name="form1" method="post">

          <fieldset style="width: 600px;">
          <table border="0">
           <tr>
            <td nowrap title="<?=@$Tk00_numpre?>"> <?=$Lk00_numpre?> </td>
            <td>
              <?php
                db_input('k00_numpre',10,$Ik00_numpre,true,'text',3);
              ?>
            </td>
           </tr>
           </tr>
            <td nowrap title="<?=@$sLabel?>">
             <b><?=$sLabel?>:</b>
            </td>
            <td>
             <?php
               db_input($sCampo,10,"",true,'text',$db_opcao2,"");
             ?>
            </td>
           </tr>
           <tr>
            <td nowrap title="<?=$Tk00_perc?>">
              <?=$Lk00_perc?>
            </td>
            <td>
              <?php
                db_input('k00_perc',10,$Ik00_perc,true,'text',$db_opcao,"");
              ?>
            </td>
           </tr>
           <tr>
             <td><b>Percentual Bloqueado:</b></td>
             <td><?=$nPercentualBloqueado?></td>
           </tr>
           <tr>
             <td><b>Obs.:</b></td>
             <td><?php db_textarea("obs",2,50,null,true,"text",1)?></td>
           </tr>
          </table>
          </fieldset>

          <table>
           <tr>
            <td colspan="2" align="center">

              <input name="<?=($db_opcao==1?"incluir":"excluir")?>"
                     type="button"
                     onclick="js_valida('<?=($db_opcao==1?"incluir":($db_opcao==2?"alterar":"excluir"))?>')"
                     id="db_opcao"
                     value="<?=($db_opcao==1?"Incluir":($db_opcao==2?"Alterar":"Excluir"))?>"
                     <?=($db_botao==false?"disabled":"")?>  >

              <input name="novo"
                     type="button"
                     id="cancelar"
                     value="Novo"
                     onclick="js_cancelar();"
                     <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >

              <input name="retornar"
                     type="button"
                     id="retornar"
                     value="Retornar"
                     onclick="location.href='arr4_manutencaovinculosnumpre001.php'">

              <input name="operacao"
                     value=""
                     type="hidden"
                     id="db_opcao" >

              <input name="tipo"
                     value="<?=$oPost->tipo?>"
                     type="hidden"
                     id="tipo" >

              <input name="numpre"
                     value="<?=$oPost->numpre?>"
                     type="hidden"
                     id="tipo" >

            </td>
           </tr>
          </table>

          <table>
           <tr>
            <td valign="top"  align="center">
            <?php
              $chavepri= array($sCampo=>@$$sCampo);
              $cliframe_alterar_excluir->chavepri      = $chavepri;
	            $cliframe_alterar_excluir->sql           = $oClasseVinculo->sql_query_file($k00_numpre);
	            $cliframe_alterar_excluir->campos        = "k00_numpre, {$sCampo}, k00_perc";
	            $cliframe_alterar_excluir->legenda       = "Vinculos Existentes";
	            $cliframe_alterar_excluir->iframe_height = "300";
	            $cliframe_alterar_excluir->iframe_width  = "600";
	            $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
            ?>
            </td>
           </tr>
          </table>
        </center>
      </form>
    </center>
	  </td>
  </tr>
</table>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
<script>

function js_cancelar() {
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}

function js_valida(operacao) {

	var nPercentualBloqueado = <?=$nPercentualBloqueado?>;

	if ($F("k00_perc") > 100 || (nPercentualBloqueado > 0 && parseFloat($F("k00_perc"))+parseFloat(nPercentualBloqueado) > 100 && $F("k00_perc") != 100) ) {
		alert("Percentual informado maior que 100%!");
		return false;
	}

	if (confirm('Serão recalculadas todos os percentuais de <?=$sLabel?> envolvidos para o numpre.\nConfirma Operação?')) {
		if ($F("k00_perc") == 100) {
			if (confirm('Foi informado o percentual de 100% para a <?=$sLabel?>!\nOs demais vinculos serão excluídos!\nConfirma Operação?')) {
				js_submit(operacao);
			}
		}	else {
			js_submit(operacao);
		}
	}

}

function js_submit(operacao) {
	document.form1.operacao.value = operacao;
	document.form1.submit();
}

</script>
<?php
if (isset($operacao) && $operacao != "") {
	 db_msgbox($sErroMsg);
}
?>
