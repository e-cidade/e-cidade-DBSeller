<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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

if( isset( $_GET['como'] ) ){
	$optionComo = $_GET['como'];
}else{
	$optionComo = "";
}

require_once (modification("libs/db_utils.php"));
require_once (modification("libs/db_stdlib.php"));
require_once (modification("libs/db_conecta.php"));
require_once (modification("libs/db_sessoes.php"));
require_once (modification("libs/db_usuariosonline.php"));
require_once (modification("dbforms/db_funcoes.php"));
require_once (modification("classes/db_fis_lancusu_classe.php"));
require_once (modification("classes/db_fis_paragrafolanc_classe.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
	echo "<script>location.href='fis1_fis_lancamento005.php?como=$optionComo'</script>";
	exit;
}

db_postmemory($HTTP_POST_VARS);

$cllancamento      = new cl_fis_lancamento;
$cllanclocal       = new cl_fis_lanclocal;
$cllancexec        = new cl_fis_lancexec;
$cllancinscr       = new cl_fis_lancinscr;
$cllancmatric      = new cl_fis_lancmatric;
$cllanccgm         = new cl_fis_lanccgm;
$cllancfiscal      = new cl_fis_lancfiscal;
$cllancsanitario   = new cl_fis_lancsanitario;
$clprocfiscallanc  = new cl_fis_procfiscallanc;
$clfiscalcgm       = new cl_fis_fiscalcgm;
$clfiscalmatric    = new cl_fis_fiscalmatric;
$clfiscalinscr     = new cl_fis_fiscalinscr;
$clfiscalsanitario = new cl_fis_fiscalsanitario;
$cllancusu         = new cl_fis_lancusu;
$clparagrafolanc   = new cl_fis_paragrafolanc;

$clenderecopecas = db_utils::getDao("fis_enderecopecas");

$db_opcao = 1;
$db_botao = true;

// Se Existir Processo Fiscal, Verifica Origem
if( isset($procfiscal) && $procfiscal != "" ){
	$sSqlTipoProc = $clprocfiscallanc->getTipoProcessoFiscal( $procfiscal );


	$rsTipoProc   = db_query($sSqlTipoProc);
	db_fieldsmemory($rsTipoProc,0);

	if ( $q02_inscr != "" ){
			unset( $z01_numcgm );
	}else if( $j01_matric != "" ){
			unset( $z01_numcgm );
	}else if( $y104_codsani != "" ){
			unset( $z01_numcgm );
	}
}


if( (isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"]) == "Incluir" ){

	$sqlerro = false;

	db_inicio_transacao();

	$cllancamento->nl01_instit = db_getsession('DB_instit') ;
	$cllancamento->incluir( $nl01_codlanc );
	$sErroMsg 	 = $cllancamento->erro_msg;
	if ( $cllancamento->erro_status == 0 ){
		$sqlerro=true;
	}
	$nl01_codlanc = $cllancamento->nl01_codlanc;


	// Se For Por Processo Fiscal
	if( $procfiscal !="" && $sqlerro == false ){

		$clprocfiscallanc->nl09_procfiscal = $procfiscal;
		$clprocfiscallanc->nl09_lanc       = $nl01_codlanc;
		$clprocfiscallanc->incluir(null);
		if ( $clprocfiscallanc->erro_status == 0 ){
			$sqlerro = true;
			$sErroMsg = $clprocfiscallanc->erro_msg;
		}

		if ($sqlerro==false){
			$cllancusu->incluir($nl01_codlanc,db_getsession('DB_id_usuario'));
		}

	}

	// Endereço do Municipio
	if( $sqlerro == false && $nl02_codigo != "" && $nl02_codi != "" ){

		$cllanclocal->nl02_codlanc	= $nl01_codlanc;
		$cllanclocal->nl02_codigo 	= $nl02_codigo;
		$cllanclocal->nl02_codi  	= @$nl02_codi;
		$cllanclocal->nl02_numero	= $nl02_numero;
		$cllanclocal->nl02_compl	= $nl02_compl;

		$cllanclocal->incluir($nl01_codlanc);

		if ( $cllanclocal->erro_status == 0 ){
			$sqlerro  = true;
			$sErroMsg = $cllanclocal->erro_msg;
		}

	}else if ( $sqlerro == false ){

  		// Caso Nao for endereco do municipio
		$clenderecopecas->end01_codpeca  = $nl01_codlanc;
		$clenderecopecas->end01_tipopeca = "L";
		$clenderecopecas->end01_rua      = $j14_nome;
		$clenderecopecas->end01_numero   = $nl02_numero;
		$clenderecopecas->end01_compl    = $nl02_compl;
		$clenderecopecas->end01_bairro   = $j13_descr;

		$clenderecopecas->incluir();

		if( $clenderecopecas->erro_status == "0" ){
			$sqlerro  = true;
			$sErroMsg = $clenderecopecas->erro_msg;
		}
	}


	if ( $sqlerro == false ){

		$cllancexec->nl03_codlanc  = $nl01_codlanc;
		$cllancexec->nl03_codigo   = $nl03_codigo;
		$cllancexec->nl03_codi     = @$nl03_codi;
		$cllancexec->nl03_numero   = $nl03_numero;
		$cllancexec->nl03_compl	   = $nl03_compl;

		$cllancexec->incluir( $nl01_codlanc );
		if ( $cllancexec->erro_status == 0 ){
			$sqlerro  = true;
			$sErroMsg = $cllancexec->erro_msg;
		}
	}

	if ( $sqlerro == false ){

		if( isset($z01_numcgm) && $z01_numcgm != "" ){

			$cllanccgm->nl06_numcgm = $z01_numcgm;
			$cllanccgm->incluir($nl01_codlanc);
			if( $cllanccgm->erro_status == 0 ){
				$sErroMsg = $cllanccgm->erro_msg;
				$sqlerro  = true;
			}

		}else if( isset($j01_matric) && $j01_matric != "" ){

			$cllancmatric->nl05_matric = $j01_matric;
			$cllancmatric->incluir($nl01_codlanc);
			if( $cllancmatric->erro_status == 0 ){
				$sErroMsg = $cllancmatric->erro_msg;
				$sqlerro  = true;
			}

		}else if( isset($q02_inscr)  && $q02_inscr  != "" ){

			$cllancinscr->nl04_inscr=$q02_inscr;
			$cllancinscr->incluir($nl01_codlanc);
			if( $cllancinscr->erro_status == 0 ){
				 $sErroMsg = $cllancinscr->erro_msg;
				 $sqlerro  = true;
			}
		}elseif( isset($y80_codsani)  && $y80_codsani  != "" ){

			$cllancsanitario->y55_codsani = $y80_codsani;
			$cllancsanitario->incluir($nl01_codlanc);
			if( $cllancsanitario->erro_status == 0 ){
				$sqlerro  = true;
				$sErroMsg = $cllancsanitario->erro_msg;
			}
		}elseif( isset($y30_codnoti)  && $y30_codnoti  != "" ){

			$cllancfiscal->y51_codnoti = $y30_codnoti;
			$cllancfiscal->incluir($nl01_codlanc);
			if( $cllancfiscal->erro_status == 0 ){
				$sErroMsg = $cllancfiscal->erro_msg;
				$sqlerro  = true;
			}else{

				/**
				 * Verifica a origem da notificação
				 */
				//matricula
				$rsMatric = $clfiscalmatric->sql_record($clfiscalmatric->sql_query($y30_codnoti));
				if ( $clfiscalmatric->numrows > 0 ) {

					$oFiscalMatric = db_utils::fieldsmemory($rsMatric,0);
					$cllancmatric->nl05_matric = $oFiscalMatric->y35_matric;
					$cllancmatric->incluir($nl01_codlanc);
					if ( $cllancmatric->erro_status==0 ) {
						$sErroMsg = $cllancmatric->erro_msg;
						$sqlerro  = true;
					}
				}

				//inscrição
				$rsInscr = $clfiscalinscr->sql_record($clfiscalinscr->sql_query($y30_codnoti));
				if ( $clfiscalinscr->numrows > 0 ) {
					$oFiscalInscr = db_utils::fieldsmemory($rsInscr,0);
					$cllancinscr->nl04_inscr=$oFiscalInscr->q02_inscr;
					$cllancinscr->incluir($nl01_codlanc);
					if ( $cllancinscr->erro_status==0 ) {
						$sErroMsg=$cllancinscr->erro_msg;
						$sqlerro = true;
					}
				}

				//sanitario
				$rsSanitario = $clfiscalsanitario->sql_record($clfiscalsanitario->sql_query($y30_codnoti));
				if ( $clfiscalsanitario->numrows > 0 ) {

					$oFiscalSanitario = db_utils::fieldsmemory($rsSanitario,0);
					$cllancsanitario->y55_codsani=$oFiscalSanitario->y80_codsani;
					$cllancsanitario->incluir($nl01_codlanc);
					if ( $cllancsanitario->erro_status==0 ) {
						$sqlerro = true;
						$sErroMsg=$cllancsanitario->erro_msg;
					}
				}

				//cgm
				$rsCgm = $clfiscalcgm->sql_record($clfiscalcgm->sql_query($y30_codnoti));
				if ( $clfiscalcgm->numrows > 0 ) {

					$oFiscalCgm = db_utils::fieldsmemory($rsCgm,0);
					$cllanccgm->nl06_numcgm = $oFiscalCgm->y36_numcgm;
					$cllanccgm->incluir($nl01_codlanc);
					if ( $cllanccgm->erro_status==0 ) {
						$sErroMsg=$cllanccgm->erro_msg;
						$sqlerro = true;
					}
				}

			}
		}
	}
 	if($sqlerro==false){
        $rowParagrafo = count($paragrafoTipo);

        $pSql     = 'select * from fiscalizacao.fis_paragrafolanc where pl30_codlanc = '.$nl01_codlanc;
        $pRs      = db_query($pSql);
        $pRowsDel = pg_num_rows($pRs);
        $pDel     = array();

        if($pRowsDel > 0){
            for($i=0; $i < pg_num_rows($pRs); $i++){
                db_fieldsmemory($pRs, $i);
                $pDel[] = $pl30_codigo;
            }
        }

        $pArr = array();
        for($i=0; $i < $rowParagrafo; $i++){
            $clparagrafolanc->pl30_codigo    = $paragrafoCod[$i];
            $clparagrafolanc->pl30_codlanc   = $nl01_codlanc;
            $clparagrafolanc->pl30_paragrafo = $paragrafoTipo[$i];
            $clparagrafolanc->pl30_texto     = $paragrafoTexto[$i];
            $clparagrafolanc->pl30_usu       = db_getsession('DB_id_usuario');

            if($paragrafoCod[$i] == ''){
                $clparagrafolanc->incluir();

                if($clparagrafolanc->erro_status==0){
                    $sqlerro=true;
                    $erro_msg=$clparagrafolanc->erro_msg;
                    break;
                }
                }else{
                    $clparagrafolanc->alterar();
                    if($clparagrafolanc->erro_status==0){
                        $sqlerro=true;
                        $erro_msg=$clparagrafolanc->erro_msg;
                        break;
                    }
                    $pArr[] = $paragrafoCod[$i];
                }
            }
            if($sqlerro==false){
                $delArr = array_diff($pDel, $pArr);

                foreach($delArr as $key => $value) {
                    $clparagrafolanc->pl30_codigo = $value;
                    $clparagrafolanc->excluir();

                    if($clparagrafolanc->erro_status==0){
                        $sqlerro=true;
                        $erro_msg=$clparagrafolanc->erro_msg;
                        break;
                    }
                }
            }
    }
	db_fim_transacao( $sqlerro );

}
if( !isset( $pri ) ){
	include (modification("fis1_fis_lancamento004.php"));
	exit;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
	<div class="container">
		<?php
		 include (modification("forms/db_frm_fis_lancamento.php"));
		?>
	</div>
</body>
</html>
<script type="text/javascript">
js_setatabulacao();
</script>
<?php

if(isset($rnum) && $rnum != ""){

		if(isset($rorigem) && $rorigem == "cgm"){
			$rsResult = $cllanccgm->sql_record($cllanccgm->sql_query(null,"*",null," nl06_numcgm = $rnum"));
			$numrows  = $cllanccgm->numrows;
			if($numrows > 0){

			 db_msgbox("Existem notificações anteriores para este CGM");
			 echo "<script>
								js_pesquisare($rnum,'cgm');
						 </script>";
			}
		}elseif(isset($rorigem) && $rorigem == "matric"){

			$rsResult = $cllancmatric->sql_record($cllancmatric->sql_query(null,"*",null," nl06_numcgm = $rnum"));
			$numrows  = $cllancmatric->numrows;
			if($numrows > 0){

			 db_msgbox("Existem notificações anteriores para esta MATRICULA");
			 echo "<script>
								js_pesquisare($rnum,'matric');
						 </script>";
			}
		}elseif(isset($rorigem) && $rorigem == "inscr"){

			$rsResult = $cllancinscr->sql_record($cllancinscr->sql_query(null,"*",null," nl04_inscr = $rnum"));
			$numrows  = $cllancinscr->numrows;
			if($numrows > 0){

			 db_msgbox("Existem notificações anteriores para esta INSCRIÇÂO");
			 echo "<script>
									 js_pesquisare($rnum,'inscr');
						</script>";
			}
		}elseif(isset($rorigem) && $rorigem == "sani"){

			$rsResult = $cllancsanitario->sql_record($cllancsanitario->sql_query(null,"*",null," nl04_inscr = $q02_inscr"));
			$numrows  = $cllancsanitario->numrows;
			if($numrows > 0){
			 db_msgbox("Existem notificações anteriores para este SANITARIO");
			 echo "<script>
								 js_pesquisare($rnum,'sani');
						 </script>";
			}
		}
}

if( (isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir" ){

	db_msgbox($sErroMsg);
	if($sqlerro==true){

		$db_botao=true;
		echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
		if($cllancamento->erro_campo!=""){

			echo "<script> document.form1.".$cllancamento->erro_campo.".style.backgroundColor='#99A9AE';</script>";
			echo "<script> document.form1.".$cllancamento->erro_campo.".focus();</script>";
		}elseif($cllancexec->erro_campo!=""){

			echo "<script> document.form1.".$cllancexec->erro_campo.".style.backgroundColor='#99A9AE';</script>";
			echo "<script> document.form1.".$cllancexec->erro_campo.".focus();</script>";
		}elseif($cllanclocal->erro_campo!=""){

			echo "<script> document.form1.".$cllanclocal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
			echo "<script> document.form1.".$cllanclocal->erro_campo.".focus();</script>";
		}
	}else{

		echo "
				 <script>
				 function js_src(){
					 parent.iframe_lancamento.location.href   = 'fis1_fis_lancamento002.php?chavepesquisa=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_lanclevanta.location.href  = 'fis1_fis_lanclevanta001.php?nl01_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_lanctipo.location.href     = 'fis1_fis_lanctipo001.php?nl18_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_receitas.location.href     = 'fis1_fis_lancrec001.php?nl18_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_fiscais.location.href      = 'fis1_fis_lancusu001.php?nl14_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_testem.location.href       = 'fis1_fis_lanctestem001.php?nl21_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_responsavel.location.href  = 'fis1_fis_lancrespons001.php?nl12_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 // parent.iframe_calculo.location.href   = 'fis1_fis_lanccalc001.php?nl01_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
					 parent.iframe_precalculo.location.href   = 'fis1_fis_lancprecalc001.php?nl01_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n

					 parent.mo_camada('lanclevanta');

					 parent.document.formaba.lanclevanta.disabled = false;
					 parent.document.formaba.lanctipo.disabled    = false;
					 parent.document.formaba.receitas.disabled    = false;
					 parent.document.formaba.fiscais.disabled     = false;
					 parent.document.formaba.testem.disabled      = false;
					 parent.document.formaba.responsavel.disabled = false;
					 // parent.document.formaba.calculo.disabled  = false;
					 parent.document.formaba.precalculo.disabled  = false;
				 }
				 js_src();
				 </script>
			 ";
	}
}
?>
