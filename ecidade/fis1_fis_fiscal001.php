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

require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("classes/db_fis_fiscal_classe.php");
require_once  modification("classes/db_fis_fiscalocal_classe.php");
require_once  modification("classes/db_fis_fiscexec_classe.php");
require_once  modification("classes/db_fis_fiscalinscr_classe.php");
require_once  modification("classes/db_fis_fiscalmatric_classe.php");
require_once  modification("classes/db_fis_fiscalvistorias_classe.php");
require_once  modification("classes/db_fis_fiscalsanitario_classe.php");
require_once  modification("classes/db_fis_fiscalcgm_classe.php");
require_once  modification("classes/db_fis_procfiscalnotificacao_classe.php");
require_once  modification("dbforms/db_funcoes.php");
require_once  modification("classes/db_fis_fiscalintimacao_classe.php");
require_once  modification("classes/db_fis_fiscalusuario_classe.php");

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
// -------------


$clenderecopecas = db_utils::getDao("fis_enderecopecas");
$tipoEnd     = ((isset($intimacao) && $intimacao == 1) ? 'I' : 'N');
if (!isset($abas)) {
  echo "<script>location.href='fis1_fis_fiscal005.php?como=$como$getIntimacao'</script>";
  exit;
}

// echo '<pre>';in
// print_r($_POST);
// echo '</pre>';
// exit;
db_postmemory($HTTP_POST_VARS);
$dataAtual = date('Y-m-d');
if(isset($procfiscal) && $procfiscal != ''){
    $sTipoProc  = " select y103_inscr as q02_inscr, y102_matric as j01_matric, y104_codsani as y80_codsani, y101_numcgm as z01_numcgm  from fiscalizacao.fis_procfiscal ";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalinscr  on y103_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalmatric on y102_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalsani   on y104_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalcgm    on y101_procfiscal = y100_sequencial";
    $sTipoProc .= "     where y100_sequencial = ".$procfiscal;
    $rsTipoProc = db_query($sTipoProc);
    db_fieldsmemory($rsTipoProc,0);
     if($q02_inscr != ""){
        unset($z01_numcgm );
    }elseif($j01_matric != ""){
        unset($z01_numcgm );
    }elseif($y104_codsani != ""){
        unset($z01_numcgm );
    }

	$sql = "SELECT y100_sequencial,
       y100_dtinicial,
       y101_numcgm,
       z01_nome,
       y103_inscr,
       y102_matric,
       y104_codsani,
       depart_protocolo AS db_depart_protocolo,
       descr_depart AS db_descr_depart,
       y100_coddepto AS db_depart_atual,
       CASE
           WHEN aberto >1 THEN 'Encerrado'
           ELSE 'Aberto'
       END AS dl_situacao,

  (SELECT p58_numero || '/' || p58_ano AS p58_numero
   FROM fiscalizacao.fis_procfiscalprot
   INNER JOIN protprocesso ON y105_protprocesso = p58_codproc
   WHERE y105_procfiscal = y100_sequencial) AS p58_numero,

  (SELECT p58_codproc
   FROM fiscalizacao.fis_procfiscalprot
   INNER JOIN protprocesso ON y105_protprocesso = p58_codproc
   WHERE y105_procfiscal = y100_sequencial) AS DB_p58_codproc
FROM
  (SELECT DISTINCT y100_sequencial,
                   y100_dtinicial,
                   y101_numcgm,
                   z01_nome,
                   y103_inscr,
                   y102_matric,
                   y104_codsani,
                   y100_coddepto,

     (SELECT p61_coddepto
      FROM protprocesso
      INNER JOIN procandam ON p58_codandam = p61_codandam
      INNER JOIN fiscalizacao.fis_procfiscalprot ON y105_protprocesso = procandam.p61_codproc
      WHERE fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial) AS depart_protocolo,

     (SELECT descrdepto
      FROM protprocesso
      INNER JOIN procandam ON p58_codandam = p61_codandam
      INNER JOIN fiscalizacao.fis_procfiscalprot ON y105_protprocesso = procandam.p61_codproc
      INNER JOIN db_depart ON coddepto = p61_coddepto
      WHERE fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial) AS descr_depart,

     (SELECT count(*)
      FROM fiscalizacao.fis_procfiscalfases AS qtd
      WHERE qtd.y108_procfiscal = fis_procfiscalfases.y108_procfiscal
      GROUP BY y108_procfiscal
      HAVING count(*) > 1) AS aberto
   FROM fiscalizacao.fis_procfiscal
   LEFT JOIN fiscalizacao.fis_procfiscalfases ON y108_procfiscal = y100_sequencial
   INNER JOIN fiscalizacao.fis_procfiscalcgm ON y101_procfiscal = y100_sequencial
   INNER JOIN cgm ON y101_numcgm = z01_numcgm
   INNER JOIN fiscalizacao.fis_procfiscalfiscais ON y106_procfiscal = y100_sequencial
   INNER JOIN fiscalizacao.fis_cadfiscais ON fis_procfiscalfiscais.y106_cadfiscais = id_usuario
   INNER JOIN fiscalizacao.fis_processofiscalativo ON fis_processofiscalativo.processo_fiscal = y100_sequencial
   AND fis_processofiscalativo.fiscal = fis_procfiscalfiscais.y106_cadfiscais
   LEFT JOIN fiscalizacao.fis_datalimitefiscal ON fis_datalimitefiscal.fiscal = fis_procfiscalfiscais.y106_cadfiscais
   LEFT JOIN fiscalizacao.fis_procfiscalmatric ON y102_procfiscal = y100_sequencial
   LEFT JOIN fiscalizacao.fis_procfiscalinscr ON y103_procfiscal = y100_sequencial
   LEFT JOIN fiscalizacao.fis_procfiscalsani ON y104_procfiscal = y100_sequencial
   WHERE y100_coddepto =  ".db_getsession("DB_coddepto")."
     AND fis_cadfiscais.id_usuario = ".db_getsession('DB_id_usuario')."
     AND (fis_datalimitefiscal.data IS NULL
          OR fis_datalimitefiscal.data > '$dataAtual')
     AND (y100_sequencial  NOT IN
       (SELECT fis_processoprorrogacaofinalizacao.processo_fiscal
        FROM fiscalizacao.fis_processoprorrogacaofinalizacao WHERE  fis_processoprorrogacaofinalizacao.situacao = 2
        ORDER BY fis_processoprorrogacaofinalizacao.sequencial DESC LIMIT 1)

       OR y100_sequencial IN (SELECT fis_processoprorrogacaofinalizacao.processo_fiscal
        FROM fiscalizacao.fis_processoprorrogacaofinalizacao WHERE  fis_processoprorrogacaofinalizacao.situacao = 1  and fis_processoprorrogacaofinalizacao.sequencial = (SELECT fis_processoprorrogacaofinalizacao.sequencial
        FROM fiscalizacao.fis_processoprorrogacaofinalizacao ORDER BY fis_processoprorrogacaofinalizacao.sequencial DESC LIMIT 1) and data_prorrogacao is not null and data_prorrogacao >= CURRENT_DATE
        ORDER BY fis_processoprorrogacaofinalizacao.sequencial DESC LIMIT 1))
     AND (y100_dtfinal >= CURRENT_DATE
          OR y100_dtfinal IS NULL)
     AND fis_processofiscalativo.ativo = 't'
     AND y100_instit =  ".db_getsession("DB_instit")."
     and y100_sequencial = $procfiscal) AS x order by y100_sequencial desc";

	$result = pg_query($sql);
	$linhas = pg_num_rows($result);

	if ($linhas == 0 ){
		db_msgbox('Processo fiscal inválido!');
		echo "<script>window.history.back();</script>";
		exit;
	}

}


$clfiscal                = new cl_fis_fiscal;
$clfiscalocal            = new cl_fis_fiscalocal;
$clfiscexec              = new cl_fis_fiscexec;
$clfiscalinscr           = new cl_fis_fiscalinscr;
$clfiscalmatric          = new cl_fis_fiscalmatric;
$clfiscalcgm             = new cl_fis_fiscalcgm;
$clfiscalsanitario       = new cl_fis_fiscalsanitario;
$clfiscalvistorias       = new cl_fis_fiscalvistorias;
$clprocfiscalnotificacao = new cl_fis_procfiscalnotificacao;
$clfiscalintimacao       = new cl_fis_fiscalintimacao;
$clparagrafointimacao    = new cl_fis_fiscalparagrafointimacao;
$clfiscalusuario         = new cl_fis_fiscalusuario;

$db_opcao = 1;
$db_botao = true;
if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir") {
	$sqlerro  = false;
  	db_inicio_transacao();

  if (!$sqlerro) {

		$clfiscal->y30_dtlanc = date('Y-m-d', db_getsession('DB_datausu'));
		$clfiscal->y30_instit = db_getsession('DB_instit');
	  $clfiscal->incluir($y30_codnoti);
	  $erro = $clfiscal->erro_msg;
	  if ($clfiscal->erro_status == 0) {
	  	$sqlerro = true;
	  }
  }

  $y30_codnoti = $clfiscal->y30_codnoti;
  if (!$sqlerro && $y12_codi != "") {
	  $y30_codnoti = $clfiscal->y30_codnoti;
	  $clfiscalocal->y12_codnoti=$y30_codnoti;
	  $clfiscalocal->y12_codigo=$y12_codigo;
	  $clfiscalocal->y12_codi=$y12_codi;
	  $clfiscalocal->y12_numero=$y12_numero;
	  $clfiscalocal->y12_compl=$y12_compl;
	  $clfiscalocal->incluir($clfiscal->y30_codnoti);
	  if ($clfiscalocal->erro_status == 0) {

	    $erro    = $clfiscalocal->erro_msg;
	    $sqlerro = true;
	  }
  }else if(!$sqlerro){
  	  $clenderecopecas->end01_codpeca  = $clfiscal->y30_codnoti;
      $clenderecopecas->end01_tipopeca = $tipoEnd;
      $clenderecopecas->end01_rua      = $j14_nome;
      $clenderecopecas->end01_numero   = $y12_numero;
      $clenderecopecas->end01_compl    = $y12_compl;
      $clenderecopecas->end01_bairro   = $j13_descr;

      $clenderecopecas->incluir();

      if( $clenderecopecas->erro_status == "0" ){
        $sqlerro = true;
        $erro    = $clenderecopecas->erro_msg;
      }
  }

  if (!$sqlerro) {
	  $clfiscexec->y13_codnoti=$y30_codnoti;
	  $clfiscexec->y13_codigo=$y13_codigo;
	  $clfiscexec->y13_codi=$y13_codi;
	  $clfiscexec->y13_numero=$y13_numero;
	  $clfiscexec->y13_compl=$y13_compl;
	  $clfiscexec->incluir($clfiscal->y30_codnoti);
	  if ($clfiscexec->erro_status == 0) {

	    $erro    = $clfiscexec->erro_msg;
	    $sqlerro = true;
	  }
  }

  if (!$sqlerro) {

	  if(isset($z01_numcgm) && $z01_numcgm != ""){

	  	if (!$sqlerro) {

		    $clfiscalcgm->y36_numcgm=$z01_numcgm;
		    $clfiscalcgm->incluir($y30_codnoti);
		    if ($clfiscalcgm->erro_status==0){

		      $erro    = $clfiscalcgm->erro_msg;
		      $sqlerro = true;
		    }
	  	}
	  } else if (isset($j01_matric) && $j01_matric != ""){

	  	if (!$sqlerro) {

		    $clfiscalmatric->y35_matric=$j01_matric;
		    $clfiscalmatric->incluir($y30_codnoti);
		    if ($clfiscalmatric->erro_status==0) {

		      $erro    = $clfiscalmatric->erro_msg;
		      $sqlerro = true;
		    }
	  	}
	  } else if (isset($q02_inscr)  && $q02_inscr  != "") {

	  	if (!$sqlerro) {

		    $clfiscalinscr->y34_inscr=$q02_inscr;
		    $clfiscalinscr->incluir($y30_codnoti);
		    if($clfiscalinscr->erro_status==0){

		      $erro    = $clfiscalinscr->erro_msg;
		      $sqlerro = true;
		    }
	  	}
	  } else if (isset($y80_codsani)  && $y80_codsani  != "") {

	  	if (!$sqlerro) {

		    $clfiscalsanitario->y37_codsani=$y80_codsani;
		    $clfiscalsanitario->incluir($y30_codnoti);
		    if($clfiscalsanitario->erro_status==0){

		      $erro    = $clfiscalsanitario->erro_msg;
		      $sqlerro = true;
		    }
	  	}
	  } else if (isset($y70_codvist)  && $y70_codvist  != "") {

	  	if (!$sqlerro) {

		    $clfiscalvistorias->y20_codvist=$y70_codvist;
		    $clfiscalvistorias->incluir($y30_codnoti,$y70_codvist);
		    if($clfiscalvistorias->erro_status==0){

		      $erro    = $clfiscalvistorias->erro_msg;
		      $sqlerro = true;
		    }
	  	}
	  }
  }

	if (!$sqlerro) {
		if($procfiscal!="") {
			print_r($procfiscal);
		  	$clprocfiscalnotificacao->y110_notificacaofiscal = $y30_codnoti;
		  	$clprocfiscalnotificacao->y110_procfiscal        = $procfiscal ;
		  	$clprocfiscalnotificacao->incluir(null);
			if($clprocfiscalnotificacao->erro_status==0){
			  	$erro    = $clprocfiscalnotificacao->erro_msg;
	      		$sqlerro = true;
	    	}

	    	// Incluo o fiscal
	    //	$fiscal = db_query("select y106_cadfiscais from fiscalizacao.fis_procfiscalfiscais where y106_procfiscal = ".$procfiscal);
        //	if(pg_num_rows($fiscal) > 0){
	        //  	for ($i = 0; $i < pg_num_rows($fiscal) ; $i++) {
	            //	db_fieldsmemory($fiscal, $i,'',true);
	            	$clfiscalusuario->incluir($y30_codnoti, db_getsession('DB_id_usuario'));
	         //   	echo "<script>parent.document.formaba.fiscais.disabled = true;</script>";
		        //}
        //	}
		}
	}

	// Ticket 108335
	if (!$sqlerro) {
		$clfiscalintimacao->in01_codnoti   = $clfiscal->y30_codnoti;

		if(isset($intimacao) && $intimacao == 1){
			$clfiscalintimacao->in01_intimacao = 'true';
		}else{
			$clfiscalintimacao->in01_intimacao = 'false';
		}

		$clfiscalintimacao->incluir();

		if($clfiscalintimacao->erro_status == 0){
			$erro    = $clfiscalintimacao->erro_msg;
			$sqlerro = true;
		}
	}
	// -------------


                if($sqlerro==false){
                $rowParagrafo = count($paragrafoTipo);

                $pSql     = 'select * from fiscalizacao.fis_paragrafointimacao where pl11_intimacao = '.$clfiscal->y30_codnoti;
                $pRs      = db_query($pSql);
                $pRowsDel = pg_num_rows($pRs);
                $pDel     = array();

                if($pRowsDel > 0){
                    for($i=0; $i < pg_num_rows($pRs); $i++){
                        db_fieldsmemory($pRs, $i);
                        $pDel[] = $pl11_codigo;
                    }
                }

                $pArr = array();
                for($i=0; $i < $rowParagrafo; $i++){
                    $clparagrafointimacao->pl11_codigo    = $paragrafoCod[$i];
                    $clparagrafointimacao->pl11_intimacao      = $clfiscal->y30_codnoti;
                    $clparagrafointimacao->pl11_paragrafo = $paragrafoTipo[$i];
                    $clparagrafointimacao->pl11_texto     = $paragrafoTexto[$i];
                    $clparagrafointimacao->pl11_usu       = db_getsession('DB_id_usuario');

                    if($paragrafoCod[$i] == ''){
                        $clparagrafointimacao->incluir();

                        if($clparagrafointimacao->erro_status==0){
                            $sqlerro=true;
                            $erro_msg=$clparagrafointimacao->erro_msg;
                            break;
                        }
                        }else{
                            $clparagrafointimacao->alterar();
                            if($clparagrafointimacao->erro_status==0){
                                $sqlerro=true;
                                $erro_msg=$clparagrafointimacao->erro_msg;
                                break;
                            }
                            $pArr[] = $paragrafoCod[$i];
                        }
                    }

                    if($sqlerro==false){
                        $delArr = array_diff($pDel, $pArr);

                        foreach($delArr as $key => $value) {
                            $clparagrafointimacao->pl11_codigo = $value;
                            $clparagrafointimacao->excluir();

                            if($clparagrafointimacao->erro_status==0){
                                $sqlerro=true;
                                $erro_msg=$clparagrafointimacao->erro_msg;
                                break;
                            }
                        }
                    }
                }
            db_fim_transacao($sqlerro);
}

if (!isset($pri)) {
  include(modification("fis1_fis_fiscal004.php"));
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
			<?php
			  include(modification("forms/db_frm_fis_fiscal.php"));
			?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<script>
js_setatabulacao();
</script>
<?php
if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir") {

	db_msgbox($erro);

  if ($sqlerro) {

    $db_botao = true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clfiscal->erro_campo!=""){
      echo "<script> document.form1.".$clfiscal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfiscal->erro_campo.".focus();</script>";
    }
  } else {

    echo "
         <script>
         function js_src(){
           parent.iframe_fiscal.location.href='fis1_fis_fiscal002.php?chavepesquisa=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.iframe_fiscaltipo.location.href='fis1_fis_fiscaltipo001.php?y31_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           // parent.iframe_receitas.location.href='fis1_fis_fiscalrec001.php?y42_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.iframe_fiscais.location.href='fis1_fis_fiscalusuario001.php?y38_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.iframe_test.location.href='fis1_fis_fisctestem001.php?y23_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           // parent.iframe_artigos.location.href='fis1_fis_fiscarquivos001.php?y26_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.mo_camada('fiscaltipo');
				   parent.document.formaba.fiscaltipo.disabled=false;
				   // parent.document.formaba.receitas.disabled=true;
				   parent.document.formaba.fiscais.disabled=false;
				   parent.document.formaba.test.disabled=false;
				   // parent.document.formaba.artigos.disabled=false;
         }
         js_src();
         </script>
       ";
  };
};
?>
