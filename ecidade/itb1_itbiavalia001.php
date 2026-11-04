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
include(modification("libs/db_utils.php"));

include(modification("dbforms/db_funcoes.php"));

include(modification("classes/db_itbi_classe.php"));
include(modification("classes/db_itbidadosimovel_classe.php"));
include(modification("classes/db_itbiavalia_classe.php"));
include(modification("classes/db_itbiavaliaformapagamentovalor_classe.php"));
include(modification("classes/db_paritbi_classe.php"));
require_once(modification("classes/db_iptuant_classe.php"));
require_once(modification("classes/db_itbimatric_classe.php"));

use ECidade\Tributario\ITBI\Repository\ItbitaxasitbiRepository;
use ECidade\Tributario\ITBI\Model\Itbitaxasitbi;
use ECidade\Tributario\ITBI\Repository\ItbitaxasavaliaRepository;
use ECidade\Tributario\ITBI\Model\Itbitaxasavalia;


$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clitbiavalia			         = new cl_itbiavalia();
$clitbiavaliaformapagamentovalor = new cl_itbiavaliaformapagamentovalor();
$clitbi		 	  	 	  	     = new cl_itbi();
$clitbidadosimovel				 = new cl_itbidadosimovel();
$clparitbi 		   				   = new cl_paritbi();
$cl_iptucalc               = new cl_iptucalc();
$cl_iptucale               = new cl_iptucale();
$cliptuant 		   		   = new cl_iptuant();
$clitbimatric              = new cl_itbimatric();

$tipo     = "";
$db_opcao = 2;
$db_botao = false;
$lSqlErro = false;

$anousu = db_getsession('DB_anousu');


if (isset($oPost->notificacao)) {
    $guia = $oPost->it14_guia;

    unset($oPost);
    $_POST = array();

    db_inicio_transacao();

    $clitbi->it01_guia = $guia;
    $clitbi->it01_notificado = 't';
    $clitbi->alterar($guia);

    if ($clitbi->erro_status == 0 ) {
        $lSqlErro = true;
        db_msgbox("Erro ao informar a notificação. Erro: {$clitbi->erro_msg}");
    } else {
        db_msgbox("A guia pode ser liberada!");
    }

    db_fim_transacao($lSqlErro);
}

if( isset($oPost->liberar) ){

  db_inicio_transacao();


  if (!$lSqlErro) {

    $clitbiavalia->it14_guia		    = $oPost->it14_guia;
    $clitbiavalia->it14_dtvenc	        = "{$oPost->it14_dtvenc_ano}-{$oPost->it14_dtvenc_mes}-{$oPost->it14_dtvenc_dia}";
    $clitbiavalia->it14_dtliber         = date('Y-m-d',db_getsession('DB_datausu'));
    $clitbiavalia->it14_obs		        = $oPost->it14_obs;

	if (isset($oPost->it01_valortransacao_avalia)){
      $clitbiavalia->it14_valoravalter    = '0';
      $clitbiavalia->it14_valoravalconstr = '0';
      $clitbiavalia->it14_valoraval       = $oPost->it01_valortransacao_avalia;
	} else {
      $clitbiavalia->it14_valoraval       = $oPost->it01_valorterreno_avalia + $oPost->it01_valorconstr_avalia;
      $clitbiavalia->it14_valoravalter    = $oPost->it01_valorterreno_avalia;
      $clitbiavalia->it14_valoravalconstr = $oPost->it01_valorconstr_avalia;
	}

    $clitbiavalia->it14_id_usuario 	    = db_getsession("DB_id_usuario");
    $clitbiavalia->it14_hora       	    = db_hora();

    $imposto_avalia = str_replace(".", "", $oPost->imposto_avalia);
    $imposto_avalia = str_replace(",", ".", $imposto_avalia);
    $clitbiavalia->it14_valorpaga       = $imposto_avalia;

    $clitbiavalia->it14_desc       	    = $oPost->desconto_avalia;

    $clitbiavalia->incluir($oPost->it14_guia);

    if ( $clitbiavalia->erro_status == 0 ) {
      $lSqlErro = true;
    }

    $sMsgErro = $clitbiavalia->erro_msg;
  }
  if ( !$lSqlErro ) {

    $aListaFormaPag = explode("|",$oPost->listaFormas);

    foreach ( $aListaFormaPag as $aChave){

  	  $aListaValorFormaPag =explode("X",$aChave);

  	  // $aListaValorFormaPag[0]  -- Código da Forma de Pagamento da Transação
  	  // $aListaValorFormaPag[1]  -- Valor  da Forma de Pagamento da Transação

  	  $clitbiavaliaformapagamentovalor->it24_itbitransacaoformapag = $aListaValorFormaPag[0];
  	  $clitbiavaliaformapagamentovalor->it24_itbiavalia 	  	   = $clitbiavalia->it14_guia;
 	  $clitbiavaliaformapagamentovalor->it24_valor				   = $aListaValorFormaPag[1];
 	  $clitbiavaliaformapagamentovalor->incluir(null);

 	  $sMsgErro = $clitbiavaliaformapagamentovalor->erro_msg;

 	  if ( $clitbiavaliaformapagamentovalor->erro_status == 0 ) {
 		$lSqlErro = true;
 	    break;
 	  }
 	}
  }

    $itbitaxasavaliaRepository = ItbitaxasavaliaRepository::getInstance();
    $itbitaxasavalia = new Itbitaxasavalia();
    $itbitaxasavalia->setGuia($oPost->it14_guia);

    $arrayTaxas = JSON::create()->parse(str_replace("\\", "", $oPost->aTaxas));

    foreach ($arrayTaxas as $oTaxas) {
        $itbitaxasavalia->setTaxaslancadas($oTaxas->codigo);
        $itbitaxasavalia->setValor($oTaxas->valor);
        $itbitaxasavalia->setCalculasobre($oTaxas->calculaSobre);

        if ($oTaxas->tipo == 2) {
            $aliquota = $oTaxas->aliquota;
        } else {
            $aliquota = null;
        }

        $itbitaxasavalia->setAliquota($aliquota);

        $itbitaxasavaliaRepository->persist($itbitaxasavalia);
    }

    db_fim_transacao($lSqlErro);


} else if ( isset($oGet->chavepesquisa) && trim($oGet->chavepesquisa) != "" ) {

  $rsDadosITBI   = $clitbi->sql_record($clitbi->sql_query_dados($oGet->chavepesquisa));
  $iNumRowsITBI  = $clitbi->numrows;

  if ($clitbi->numrows > 0) {

     db_fieldsmemory($rsDadosITBI,0);

     if ( isset($it05_guia) && trim($it05_guia) ){
	   $oGet->tipo = "urbano";

       $rItbiMatric = db_query($clitbimatric->sql_query_file($it05_guia, null, "it06_matric AS j01_matric"));

       if ($rItbiMatric AND pg_numrows($rItbiMatric) > 0) {
           db_fieldsmemory($rItbiMatric,0);

           $rIptuant  = db_query($cliptuant->sql_query_file($j01_matric));

           if (!$rIptuant) {
               throw new Exception("Erro ao buscar a referência anterior.\n\nErro: ".pg_last_error());
           }

           db_fieldsmemory($rIptuant, 0);
       }
     } else {
       $oGet->tipo = "rural";
     }

     $it01_tipotransacao_avalia  = $it01_tipotransacao;
     $it04_descr_avalia 		 = $it04_descr;
     $it14_guia 				 = $oGet->chavepesquisa;
     $desconto_avalia			 = $it04_desconto;

     for ( $iInd=0; $iInd < $iNumRowsITBI; $iInd++  ) {

  	   $oDadosNome = db_utils::fieldsMemory($rsDadosITBI,$iInd);

	   if ( $oDadosNome->it03_tipo == "T" && $oDadosNome->it03_princ == "t") {
	     $transmitenteprinc = $oDadosNome->it03_nome;
       } else if ( $oDadosNome->it03_tipo == "C" && $oDadosNome->it03_princ == "t") {
    	 $adquirenteprinc   = $oDadosNome->it03_nome;
       }
     }

    $rsParam = $clparitbi->sql_record($clparitbi->sql_query_file(db_getsession('DB_anousu'),"it24_diasvctoitbi, it24_solicitanotificacao, it24_comparavaloresavaliacao"));

	 if ( $clparitbi->numrows > 0 ) {
	   $oParam = db_utils::fieldsMemory($rsParam,0);
	 } else {
	   db_msgbox("Favor configurar os  parâmetros de ITBI!");
	   db_redireciona("itb1_paritbi001.php");
   }

      $db_opcao_avaliados = $db_opcao;

      if ( isset($it05_guia) && trim($it05_guia) ){
          if ($oParam->it24_comparavaloresavaliacao == "t") {
              $rIptucalc = db_query($cl_iptucalc->sql_query_file($anousu, $it06_matric, "j23_vlrter"));

              if (!$rIptucalc) {
                  throw new Exception("Erro ao buscar o os dados da tabela iptucalc");
              } else {
                  $aIptucalc = pg_fetch_assoc($rIptucalc);
                  if ($aIptucalc != false) {
                      db_postmemory($aIptucalc);
                  }
              }

              $rIptucale = db_query($cl_iptucale->sql_query(null, null, null, "SUM(j22_valor) AS j22_valor", null, " j22_anousu = {$anousu} AND j22_matric = {$it06_matric} AND j39_dtdemo IS NULL "));

              if (!$rIptucale) {
                  throw new Exception("Erro ao buscar o os dados da tabela iptucale");
              } else {
                  $aIptucale = pg_fetch_assoc($rIptucale);

                  if ($aIptucale != false) {
                      db_postmemory($aIptucale);
                  }
              }

              $valorVenalTotal = $j22_valor + $j23_vlrter;

              $bValorInformadoMaior = true;

              if (floatval($valorVenalTotal) > floatval($it01_valortransacao))
              {
                  $it01_valorterreno_avalia = $j23_vlrter;
                  $it01_valorconstr_avalia = $j22_valor;
                  $it01_valortransacao_avalia = $valorVenalTotal;

                  $bValorInformadoMaior = false;
              } else {
                  $it01_valorterreno_avalia = $it01_valorterreno;
                  $it01_valorconstr_avalia = $it01_valorconstr;
                  $it01_valortransacao_avalia = $it01_valortransacao;
              }

              $db_opcao_avaliados = 3;

              $it01_valorterreno_avalia = number_format($it01_valorterreno_avalia, 2, ",", ".");
              $it01_valorconstr_avalia = number_format($it01_valorconstr_avalia, 2, ",", ".");
              $it01_valortransacao_avalia = number_format($it01_valortransacao_avalia, 2, ",", ".");

              $j23_vlrter = number_format($j23_vlrter, 2, ",", ".");
              $j22_valor = number_format($j22_valor, 2, ",", ".");
              $valorVenalTotal = number_format($valorVenalTotal, 2, ",", ".");
          }
      }

  	 $iDia = date('d',db_getsession('DB_datausu')) + $oParam->it24_diasvctoitbi;
  	 $iMes = date('m',db_getsession('DB_datausu'));
  	 $iAno = date('Y',db_getsession('DB_datausu'));

     $it14_dtvenc_dia = date('d',mktime(0,0,0,$iMes,$iDia,$iAno));
     $it14_dtvenc_mes = date('m',mktime(0,0,0,$iMes,$iDia,$iAno));
     $it14_dtvenc_ano = date('Y',mktime(0,0,0,$iMes,$iDia,$iAno));

     $itbitaxasitbiRepository = ItbitaxasitbiRepository::getInstance();
     $itbitaxasitbi = new Itbitaxasitbi();

     $itbitaxasitbi->setItbi($oGet->chavepesquisa);

     $oDadosTaxaGuia = $itbitaxasitbiRepository->getDados($itbitaxasitbi);

     $habilitaBotaoLiberar = true;

     if ($oParam->it24_solicitanotificacao == "t") {
        if ($it01_notificado == "f") {
            $habilitaBotaoLiberar = false;
        }
     }

      $it01_valorterreno = number_format($it01_valorterreno, 2, ",", ".");
      $it01_valorconstr = number_format($it01_valorconstr, 2, ",", ".");
      $it01_valortransacao = number_format($it01_valortransacao, 2, ",", ".");
  } else {
  	db_msgbox("Nenhum dado encontrado!");
  	db_redireciona("itb1_itbiavalia001.php");
  }
} else {
  $oGet->tipo = "urbano";
  $db_opcao   = 3;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript"src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<script language="JavaScript" type="text/javascript"src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript"src="scripts/datagrid.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table style="padding-top:25px;" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
	  <?php
	     include(modification("forms/db_frmitbiavalia.php"));
      ?>
    </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if ( isset($oPost->liberar) ) {

  if( $lSqlErro ){

    $clitbiavalia->erro(true,false);
    $db_botao=true;

    echo "<script> document.form1.db_opcao.disabled=false;</script>";
    if($clitbiavalia->erro_campo!=""){
      echo "<script> document.form1.".$clitbiavalia->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clitbiavalia->erro_campo.".focus();</script>";
    }
  }else{
 	echo "<script>js_limpaForm();</script>";
	db_msgbox($sMsgErro);
	echo "<script>";
	echo "  if (confirm('Deseja emitir a guia?')){";
	echo "    window.open('reciboitbi.php?itbi=".$clitbiavalia->it14_guia."',\"\",\"toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,height=\"+(screen.height-100)+\",width=\"+(screen.width-100));";
	echo "  }";
	echo "</script>";
    db_redireciona("itb1_itbiavalia001.php");
  }
}

if($db_opcao==3){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
