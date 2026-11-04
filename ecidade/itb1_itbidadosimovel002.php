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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_itbipropriold_classe.php"));
require_once(modification("classes/db_itbi_classe.php"));
require_once(modification("classes/db_itbilogin_classe.php"));
require_once(modification("classes/db_itbinome_classe.php"));
require_once(modification("classes/db_itbinomecgm_classe.php"));
require_once(modification("classes/db_itbimatric_classe.php"));
require_once(modification("classes/db_itburbano_classe.php"));
require_once(modification("classes/db_itbirural_classe.php"));
require_once(modification("classes/db_itbiruralcaract_classe.php"));
require_once(modification("classes/db_itbidadosimovel_classe.php"));
require_once(modification("classes/db_itbidadosimovelsetorloc_classe.php"));
require_once(modification("classes/db_itbiformapagamentovalor_classe.php"));
require_once(modification("classes/db_itbiavalia_classe.php"));
require_once(modification("classes/db_localidaderural_classe.php"));
require_once(modification("classes/db_itbilocalidaderural_classe.php"));
require_once(modification("classes/db_iptuant_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\ITBI\Repository\ItbitaxasitbiRepository;
use ECidade\Tributario\ITBI\Model\Itbitaxasitbi;

$itbitaxasitbiRepository = ItbitaxasitbiRepository::getInstance();
$itbitaxasitbi = new Itbitaxasitbi();

$oGet  = db_utils::postMemory($_GET);
$oPost = db_utils::postMemory($_POST);

$cliptubase				         = new cl_iptubase();
$clpropri 		  		       = new cl_propri();
$clitbi 		  		         = new cl_itbi();
$clitbiavalia              = new cl_itbiavalia();
$clitbinome		  		       = new cl_itbinome();
$clitbinomecgm	  	       = new cl_itbinomecgm();
$clitbipropriold  	       = new cl_itbipropriold();
$clitbilogin			         = new cl_itbilogin();
$clitbimatric	  		       = new cl_itbimatric();
$clitbirural	  		       = new cl_itbirural();
$clitbiruralcaract 	       = new cl_itbiruralcaract();
$clitburbano 	   		       = new cl_itburbano();
$clitbidadosimovel 		     = new cl_itbidadosimovel();
$clitbidadosimovelsetorloc = new cl_itbidadosimovelsetorloc();
$clitbiformapagamentovalor = new cl_itbiformapagamentovalor();
$cllocalidaderural         = new cl_localidaderural();
$clitbilocalidaderural     = new cl_itbilocalidaderural();
$cl_iptucalc               = new cl_iptucalc();
$cl_iptucale               = new cl_iptucale();
$cliptuant 		   		   = new cl_iptuant();

$db_opcao    = 2;
$db_botao    = false;
$lSqlErro    = false;
$lItbiAvalia = false;
$lLiberar    = false;


$sBtnEnviaLiberacao = 'liberar';
$sBtnLiberacao      = 'Enviar para liberação';

$iAnoUsu   = db_getsession('DB_anousu');
$lPermMenu = db_permissaomenu($iAnoUsu, 2544, 2571);

if (isset($oGet->chavepesquisa) && !empty($oGet->chavepesquisa)) {
	$it01_guia = $oGet->chavepesquisa;
  $_SESSION['itbi_guiachavepesquisa'] = $it01_guia;
}

if (isset($it01_guia) && !empty($it01_guia)) {

  $rsItbiAvalia = $clitbiavalia->sql_record($clitbiavalia->sql_query_file($it01_guia, "*", null, ""));

  if ($clitbiavalia->numrows > 0) {
    $lItbiAvalia = true;
  }

  $rsItbi = $clitbi->sql_record($clitbi->sql_query_file($it01_guia, "it01_envia", null, ""));
  if ($clitbi->numrows > 0) {
    $oItbi = db_utils::fieldsMemory($rsItbi,0);
  }
}

if (isset($oItbi->it01_envia)) {

  if ($oItbi->it01_envia == 't') {

    $sBtnLiberacao      = 'Cancela envio a guia';
    $sBtnEnviaLiberacao = 'cancelar';
    if ($lPermMenu) {
      $lLiberar = true;
    }
  } else if ($oItbi->it01_envia == 'f') {

    $sBtnLiberacao      = 'Enviar para liberação';
    $sBtnEnviaLiberacao = 'liberar';
  }
}

if (isset($oPost->alterar) or isset($oPost->liberacao)) {

  db_inicio_transacao();

  $clitbi->it01_tipotransacao  = $oPost->it01_tipotransacao;
  $clitbi->it01_areaterreno	   = $oPost->it01_areaterreno;
  $clitbi->it01_areaedificada  = "0";
  $clitbi->it01_obs			       = $oPost->it01_obs;
  $clitbi->it01_areatrans	     = $oPost->it01_areatrans;
  $clitbi->it01_mail		       = $oPost->it01_mail;
  $clitbi->it01_finalizado	   = false;
  $clitbi->it01_origem		     = 1;
  $clitbi->it01_id_usuario	   = db_getsession('DB_id_usuario');
  $clitbi->it01_coddepto	     = db_getsession('DB_coddepto');
  $clitbi->it01_data		       = date('Y-m-d',db_getsession('DB_datausu'));
  $clitbi->it01_hora		       = db_hora();
  $clitbi->it01_percentualareatransmitida = $oPost->it01_percentualareatransmitida;
  $clitbi->it01_cartorioextra = $oPost->j167_sequencial;

  if($oPost->it01_processoexterno == null) {
    $clitbi->it01_processo = $oPost->it01_processo;
    $clitbi->it01_tituprocesso = $oPost->p58_requer;
    $clitbi->it01_dtprocesso = null;
  } else {
    $data = str_replace('/', '-', $oPost->it01_dtprocesso);
    $clitbi->it01_processo = $oPost->it01_processoexterno;
    $clitbi->it01_tituprocesso = $oPost->it01_tituprocesso;
    $clitbi->it01_dtprocesso = date('Y-m-d', strtotime(str_replace('/', '-', $oPost->it01_dtprocesso)));
  }



  if (isset($oPost->it01_valortransacao)) {
    $clitbi->it01_valorterreno   = null;
    $clitbi->it01_valorconstr    = null;
    $clitbi->it01_valortransacao = $oPost->it01_valortransacao;
  } else {
    $clitbi->it01_valorterreno   = $oPost->it01_valorterreno;
    $clitbi->it01_valorconstr    = $oPost->it01_valorconstr;
    $clitbi->it01_valortransacao = $oPost->it01_valorterreno + $oPost->it01_valorconstr;
  }

  $clitbi->alterar($oPost->it01_guia);

  $clitbi->it01_guia = $oPost->it01_guia;

  if ($clitbi->erro_status == 0) {
  	$lSqlErro = true;
  }

  $sMsgErro = $clitbi->erro_msg;

  if ($oPost->tipo == "urbano") {

    $clitburbano->it05_guia 	  	  = $clitbi->it01_guia;
    $clitburbano->it05_frente   	  = $oPost->it05_frente;
    $clitburbano->it05_fundos   	  = $oPost->it05_fundos;
    $clitburbano->it05_esquerdo 	  = $oPost->it05_esquerdo;
    $clitburbano->it05_direito      = $oPost->it05_direito;
    $clitburbano->it05_itbisituacao = $oPost->it05_itbisituacao;

    $clitburbano->alterar($clitbi->it01_guia);

    if($clitburbano->erro_status == 0){
      $sMsgErro = $clitburbano->erro_msg;
	  $lSqlErro = true;
    }

  } else if($oPost->tipo == "rural") {

    $clitbirural->it18_guia   	   = $clitbi->it01_guia;
    $clitbirural->it18_frente 	   = $oPost->it18_frente;
    $clitbirural->it18_fundos 	   = $oPost->it18_fundos;
    $clitbirural->it18_prof 	     = $oPost->it18_prof;
    $clitbirural->it18_localimovel = $oPost->it18_localimovel;
    $clitbirural->it18_distcidade  = $oPost->it18_distcidade;

    if (isset($oPost->it18_coordenadas) && $oPost->it18_coordenadas != "") {
      $clitbirural->it18_coordenadas = $oPost->it18_coordenadas;
    } else {
      $clitbirural->it18_coordenadas = " ";
    }

	  if ( $oPost->lFrenteLogradouro == 'n' ) {
      $clitbirural->it18_nomelograd  = " ";
	  } else {
	    $clitbirural->it18_nomelograd  = $oPost->it18_nomelograd;
	  }

    $clitbirural->it18_area		   = $oPost->it01_areaterreno;

    $clitbirural->alterar($clitbi->it01_guia);

    // Excluimos todas as localidades e apos inserimos a nova
    $clitbilocalidaderural->excluir('',' it33_guia = ' . $clitbi->it01_guia );

    $clitbilocalidaderural->it33_guia 					 = $clitbi->it01_guia;
    $clitbilocalidaderural->it33_localidaderural = $oPost->j137_sequencial;
    $clitbilocalidaderural->incluir( null );

    if ( $clitbirural->erro_status == 0 ) {
      $lSqlErro = true;
    }

    $sMsgErro   = $clitbirural->erro_msg;

	  if ( !$lSqlErro ) {

       $aListaCaracImovel = explode("|",$oPost->valorCaracImovel);

	     foreach ( $aListaCaracImovel as $aChave){

	       $aListaDadosCaracImovel =explode("X",$aChave);
	       if ($aListaDadosCaracImovel[0]=="") {
           continue;
         }
         $rsDadosCaractRural = $clitbiruralcaract->sql_record($clitbiruralcaract->sql_query_file(null,
                                                                                                 null,
                                                                                                 "*",
                                                                                                 null,
                                                                                                 " it19_guia = ".$oPost->it01_guia."
                                                                                               and it19_codigo = ".$aListaDadosCaracImovel[0]."
                                                                                               and it19_tipocaract = 1"));
         $iDadosCaractRural  = @pg_numrows($rsDadosCaractRural);

	       $clitbiruralcaract->it19_guia       = $clitbi->it01_guia;
	       $clitbiruralcaract->it19_codigo     = @$aListaDadosCaracImovel[0];
	       $clitbiruralcaract->it19_valor      = @$aListaDadosCaracImovel[1];
	       $clitbiruralcaract->it19_tipocaract = '1';
	       if ($iDadosCaractRural > 0) {
           $clitbiruralcaract->alterar($clitbi->it01_guia,$aListaDadosCaracImovel[0]);
         } else {
           $clitbiruralcaract->incluir($clitbi->it01_guia,$aListaDadosCaracImovel[0]);
           echo pg_last_error();
         }

	       if ( $clitbiruralcaract->erro_status == 0 ) {

	         $sMsgErro = $clitbiruralcaract->erro_msg;
	   	     $lSqlErro = true;
		       break;
	       }
   	   }
	  }

    if ( !$lSqlErro ) {

	     $aListaCaracUtil = explode("|",$oPost->valorCaracUtil);

	     foreach ( $aListaCaracUtil as $aChave){

	       $aListaDadosCaracUtil =explode("X",$aChave);
	       if ($aListaDadosCaracUtil[0]=="") {
           continue;
         }
         $rsDadosCaractRural = $clitbiruralcaract->sql_record($clitbiruralcaract->sql_query_file(null,
                                                                                                 null,
                                                                                                 "*",
                                                                                                 null,
                                                                                                 " it19_guia = {$oPost->it01_guia}
                                                                                               and it19_codigo = {$aListaDadosCaracUtil[0]}
                                                                                               and it19_tipocaract = 2"));
         $iDadosCaractRural  = $clitbiruralcaract->numrows;

		     $clitbiruralcaract->it19_guia   = $clitbi->it01_guia;
		     $clitbiruralcaract->it19_codigo = @$aListaDadosCaracUtil[0];
		     $clitbiruralcaract->it19_valor  = @$aListaDadosCaracUtil[1];
		     $clitbiruralcaract->it19_tipocaract = '2';
		     if ($iDadosCaractRural > 0) {
		       $clitbiruralcaract->alterar($clitbi->it01_guia,$aListaDadosCaracUtil[0]);
		     } else {
  		   	 $clitbiruralcaract->incluir($clitbi->it01_guia,$aListaDadosCaracUtil[0]);
	  	   }

		     if ( $clitbiruralcaract->erro_status == 0 ) {
		       $sMsgErro = $clitbiruralcaract->erro_msg;
		       $lSqlErro = true;
  	 	     break;
	 	     }
	     }
	  }
  }

  $rsDadosImovel = $clitbidadosimovel->sql_record($clitbidadosimovel->sql_query_file(null,"*",null," it22_itbi = {$oPost->it01_guia}"));
  $iDadosImovel  = $clitbidadosimovel->numrows;

  $clitbidadosimovel->it22_sequencial  = $oPost->it22_sequencial;
  $clitbidadosimovel->it22_itbi   	   = $clitbi->it01_guia;
  $clitbidadosimovel->it22_setor  	   = $oPost->it22_setor;
  $clitbidadosimovel->it22_quadra	     = $oPost->it22_quadra;
  $clitbidadosimovel->it22_lote   	   = $oPost->it22_lote;
  $clitbidadosimovel->it22_descrlograd = $oPost->it22_descrlograd;
  $clitbidadosimovel->it22_numero 	   = $oPost->it22_numero;
  $clitbidadosimovel->it22_compl	     = $oPost->it22_compl;
  $clitbidadosimovel->it22_matricri	   = $oPost->it22_matricri;
  $clitbidadosimovel->it22_quadrari    = $oPost->it22_quadrari;
  $clitbidadosimovel->it22_loteri 	   = $oPost->it22_loteri;

  if ( $iDadosImovel > 0 ) {
	$clitbidadosimovel->alterar($clitbidadosimovel->it22_sequencial);
  } else {
  	$clitbidadosimovel->incluir(null);
  }

  if ( $clitbidadosimovel->erro_status == 0 ) {
    $sMsgErro = $clitbidadosimovel->erro_msg;
    $lSqlErro = true;
  }

  if ( !$lSqlErro ) {

    $aListaFormaPag = explode("|",$oPost->listaFormas);

    foreach ( $aListaFormaPag as $aChave){

  	  $aListaValorFormaPag =explode("X",$aChave);

  	  // $aListaValorFormaPag[0]  -- Código da Forma de Pagamento da Transação
  	  // $aListaValorFormaPag[1]  -- Valor  da Forma de Pagamento da Transação

  	  $sWhere  = "     it26_itbitransacaoformapag = ".$aListaValorFormaPag[0];
  	  $sWhere .= " and it26_guia 				 = {$clitbi->it01_guia}";

  	  $rsConsultaForma = $clitbiformapagamentovalor->sql_record($clitbiformapagamentovalor->sql_query_file(null,"*",null,$sWhere));
  	  $iLinhasForma	   = $clitbiformapagamentovalor->numrows;

  	  $clitbiformapagamentovalor->it26_guia 				 = $clitbi->it01_guia;
  	  $clitbiformapagamentovalor->it26_itbitransacaoformapag = $aListaValorFormaPag[0];
 	    $clitbiformapagamentovalor->it26_valor				 = $aListaValorFormaPag[1];

 	    if($iLinhasForma > 0){

 	  	  $oDadosForma = db_utils::fieldsMemory($rsConsultaForma,0);

 	    	$clitbiformapagamentovalor->it26_sequencial = $oDadosForma->it26_sequencial;
 	      $clitbiformapagamentovalor->alterar($oDadosForma->it26_sequencial);

 	    } else {
 	  	  $clitbiformapagamentovalor->incluir(null);
 	    }

     /*
      *
      * Exclui as formas de pagamentos de outros tipos de transação da guia caso existam.
      *
      */
      $sWhere2  = " it26_itbitransacaoformapag not in (select it25_sequencial ";
	    $sWhere2 .= "                                      from itbitransacao   ";
	    $sWhere2 .= "                                           inner join itbitransacaoformapag   on it25_itbitransacao         = it04_codigo     ";
   	  $sWhere2 .= "                                           inner join itbiformapagamentovalor on it26_itbitransacaoformapag = it25_sequencial ";
 	    $sWhere2 .= "       					                    where it26_guia = {$clitbi->it01_guia}     ";
	    $sWhere2 .= "     						                      and it04_codigo = {$it01_tipotransacao}) ";
      $sWhere2 .= "                                       and it26_guia = {$clitbi->it01_guia}     ";

      $clitbiformapagamentovalor->excluir(null, $sWhere2);

 	    if ($clitbiformapagamentovalor->erro_status == 0 ) {
 	  	  $sMsgErro = $clitbiformapagamentovalor->erro_msg;
 		    $lSqlErro = true;
 	      break;
 	    }

 	}

  }

  if (!$lSqlErro) {

    $clitbiEnvia = new cl_itbi();

    if ($oPost->envialiberacao == 'liberar') {

      /*
       * Verificamos se a guia que estamos tentando liberar possui transmitente e adquirente cadastrados
       */
      $sSqlTransmitente = "select distinct
                                  it03_tipo
                             from itbinome
                            where it03_guia = $it01_guia
                              and it03_tipo in ('T','C')";
      $rsTransmitente   = db_query($sSqlTransmitente);

      if (pg_num_rows($rsTransmitente) < 2) {
        $sMsgErro = "Não é permitido envio de uma guia sem transmitentes e adquirentes cadastrados!";
      } else {
        $sBtnEnviaLiberacao   = 'cancelar';
        $clitbiEnvia->it01_envia   = 'true';
        $clitbiEnvia->alterar($it01_guia);

        if ($clitbiEnvia->erro_status == 0) {
          $lSqlErro = true;
        }

        $sMsgErro = $clitbiEnvia->erro_msg;
      }

    } else if ($oPost->envialiberacao == 'cancelar') {

      if (isset($lItbiAvalia) && $lItbiAvalia != false) {
        $sMsgErro = "Não é permitido cancelar o envio de uma guia já liberada!";
      } else {

        $sBtnEnviaLiberacao = 'liberar';
        $clitbiEnvia->it01_envia = 'false';
        $clitbiEnvia->alterar($it01_guia);

        if ($clitbiEnvia->erro_status == 0) {
          $lSqlErro = true;
        }

        $sMsgErro = $clitbiEnvia->erro_msg;
      }
    }
  }

  if (!$lSqlErro AND !empty($oPost->codigoTipoTaxa)) {
    $itbitaxasitbi->setItbi($it01_guia);

    $oItbitaxasitbi = $itbitaxasitbiRepository->getDados($itbitaxasitbi);

    $itbitaxasitbi->setSequencial($oItbitaxasitbi->it38_sequencial);
    $itbitaxasitbi->setTaxasitbi($oPost->codigoTipoTaxa);

    $itbitaxasitbiRepository->persist($itbitaxasitbi);
  }

  db_fim_transacao($lSqlErro);

} else if (isset($oGet->chavepesquisa)){

   $db_opcao  = 2;
   $it22_itbi = $oGet->chavepesquisa;

   $rsDadosITBI = $clitbi->sql_record($clitbi->sql_query_dados($oGet->chavepesquisa,"*, it33_localidaderural as j137_sequencial, it33_localidaderural as j137_descricao, (CASE WHEN it01_dtprocesso IS NOT NULL THEN it01_processo ELSE '' END) AS it01_processoExterno"));
   if ($clitbi->numrows > 0) {
   	 db_fieldsmemory($rsDadosITBI,0);
   	 if ( isset($it05_guia) && trim($it05_guia) ){
	     $oGet->tipo = "urbano";
   	 } else {
   	   $oGet->tipo = "rural";
   	 }
   }

  $rItbiMatric = db_query($clitbimatric->sql_query_file($it22_itbi, null, "it06_matric AS j01_matric"));

  if ($rItbiMatric AND pg_numrows($rItbiMatric) > 0) {
    db_fieldsmemory($rItbiMatric,0);

    $rIptuant  = db_query($cliptuant->sql_query_file($j01_matric));

    if (!$rIptuant) {
        throw new Exception("Erro ao buscar a referência anterior.\n\nErro: ".pg_last_error());
    }

      db_fieldsmemory($rIptuant, 0);
  }

    $it01_valorterreno = number_format($it01_valorterreno, 2, ",", ".");
    $it01_valorconstr = number_format($it01_valorconstr, 2, ",", ".");
    $it01_valortransacao = number_format($it01_valortransacao, 2, ",", ".");

    $itbitaxasitbi->setItbi($oGet->chavepesquisa);
    $oItbitaxasitbi = $itbitaxasitbiRepository->getDados($itbitaxasitbi);
    $db_botao = true;

      echo " <script>

              parent.document.formaba.dados.disabled    = false;
              parent.document.formaba.transm.disabled   = false;
              parent.document.formaba.compnome.disabled = false;
              parent.document.formaba.constr.disabled   = false;
              parent.document.formaba.anexo.disabled   = false;

              parent.iframe_transm.location.href   = 'itb1_itbinome001.php?tiponome=t&it03_guia=".$oGet->chavepesquisa."';
              parent.iframe_compnome.location.href = 'itb1_itbinomecomp001.php?tiponome=c&it03_guia=".$oGet->chavepesquisa."';
              parent.iframe_constr.location.href   = 'itb1_itbiconstr001.php?it08_guia=".$oGet->chavepesquisa."&tipo=".$oGet->tipo."';
              parent.iframe_anexo.location.href 	 = 'itb1_itbianexo001.php?itbi_guia=" . $oGet->chavepesquisa . "';

            </script>";

      echo " <script>
              parent.document.formaba.inter.disabled = false;
              parent.iframe_inter.location.href      = 'itb1_itbiintermediario001.php?tiponome=t&it03_guia=".$oGet->chavepesquisa."';
            </script>";

} else {

  $db_opcao   = 22;
  $oGet->tipo = "urbano";
}
/*
 * Plugin ajustacadastroitbi Cria
 if ($tipo == "urbano") {
     $db_opcao_plugin = 3;
 } else {
     $db_opcao_plugin_rural = 3;
 }
*/
/* M16507 - ajustacadastroitbi - Plugin que ajusta os campos do ITBI para Porto Velho */
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
<table align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
	  <?php
  	    include(modification("forms/db_frmitbidadosimovel.php"));
	  ?>
	</td>
  </tr>
</table>
</body>
</html>
<?php
if (isset($oPost->alterar) or isset($oPost->liberacao)) {

  if ($lSqlErro) {

    if (isset($oPost->alterar)) {

      db_msgbox($sMsgErro);
      $db_botao=true;
      echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
      if($clitbidadosimovel->erro_campo!=""){
        echo "<script> document.form1.".$clitbidadosimovel->erro_campo.".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1.".$clitbidadosimovel->erro_campo.".focus();</script>";
      }
    }elseif (isset($oPost->liberacao)) {

      if (isset($sMsgErro) && !empty($sMsgErro)) {
        db_msgbox($sMsgErro);
      }
    }
  } else {
    db_msgbox($sMsgErro);
    db_redireciona("itb1_itbidadosimovel002.php?chavepesquisa={$oPost->it01_guia}&tipo={$tipo}");
  }
}

if ( $db_opcao == 22 ) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
