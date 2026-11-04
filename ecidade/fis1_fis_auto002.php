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
require_once(modification("classes/db_fis_auto_classe.php"));
require_once(modification("classes/db_fis_autolocal_classe.php"));
require_once(modification("classes/db_fis_autoexec_classe.php"));
require_once(modification("classes/db_fis_procfiscalauto_classe.php"));
require_once(modification("classes/db_fis_enderecopecas_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

if(!isset($abas)){

  echo "<script>location.href='fis1_fis_auto005.php?db_opcao=2'</script>";
  exit;
}

db_postmemory($HTTP_POST_VARS);

$auto             = 1;
$clauto           = new cl_fis_auto;
$clautolocal      = new cl_fis_autolocal;
$clautoexec       = new cl_fis_autoexec;
$clprocfiscalauto = new cl_fis_procfiscalauto;
$clparagrafoauto  = new cl_fis_fiscalparagrafoauto;
$clenderecopecas  = new cl_fis_enderecopecas;
$db_opcao         = 22;
$db_botao         = false;

echo "
<script>
  parent.document.formaba.autolevanta.disabled = true;
  parent.document.formaba.autotipo.disabled    = true;
  // parent.document.formaba.receitas.disabled    = true;
  parent.document.formaba.fiscais.disabled     = true;
  parent.document.formaba.respons.disabled     = true;
  parent.document.formaba.precalculo.disabled  = true;
</script>
";

$sqlerro = false;
$passa   = false;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){

  db_inicio_transacao();
  $db_opcao = 2;
  $clauto->alterar($y50_codauto);
  if ($clauto->erro_status==0){

    $sqlerro  = true;
    $erro_msg = $clauto->erro_msg;
  }

	// exclui e inclui no procfiscalvistorias
	 $sqlprocfiscalv    = "select y111_sequencial from fiscalizacao.fis_procfiscalauto where y111_auto = $y50_codauto";
	 $resultprocfiscalv = db_query($sqlprocfiscalv);
	 $linhasprocfiscalv = pg_num_rows($resultprocfiscalv);
	 if($linhasprocfiscalv>0){

	 	 db_fieldsmemory($resultprocfiscalv,0);
	   $clprocfiscalauto->y111_sequencial = $y111_sequencial;
		 $clprocfiscalauto->excluir($y111_sequencial);
		 if($clprocfiscalauto->erro_status==0){

				$erro    = $clprocfiscalauto->erro_msg;
	      $sqlerro = true;
	   }
	 }

	 if($procfiscal !=""){

			$clprocfiscalauto->y111_procfiscal = $procfiscal;
			$clprocfiscalauto->y111_auto       = $y50_codauto;
			$clprocfiscalauto->incluir(null);
			if ($clprocfiscalauto->erro_status==0){

	      $sqlerro = true;
				$erro    = $clprocfiscalauto->erro_msg;
	    }
		}

  if( $sqlerro == false && $y14_codigo != "" && $y14_codi != "" ){
    $result = $clautolocal->sql_record($clautolocal->sql_query($y50_codauto));
    if($clautolocal->numrows > 0){

      $clautolocal->y14_codauto = $y50_codauto;
      $clautolocal->y14_codigo  = $y14_codigo;
      $clautolocal->y14_codi    = $y14_codi;
      $clautolocal->y14_numero  = $y14_numero;
      $clautolocal->y14_compl   = $y14_compl;
      $clautolocal->alterar($y50_codauto);
      if ($clautolocal->erro_status==0){

        $sqlerro  = true;
        $erro_msg = $clautolocal->erro_msg;
      }
    }else{

      $clautolocal->incluir($y50_codauto);
      if ($clautolocal->erro_status==0){

        $sqlerro  = true;
        $erro_msg = $clautolocal->erro_msg;
      }
     }
  }else{

      $clenderecopecas->end01_codpeca  = $y50_codauto;
      $clenderecopecas->end01_tipopeca = "A";
      $clenderecopecas->end01_rua      = $j14_nome;
      $clenderecopecas->end01_numero   = $y14_numero;
      $clenderecopecas->end01_compl    = $y14_compl;
      $clenderecopecas->end01_bairro   = $j13_descr;

      $rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$y50_codauto} and end01_tipopeca = 'A'");

      if( pg_num_rows($rsEndPecas) > 0 ){
        $clenderecopecas->alterar();
      }else{
        $clenderecopecas->incluir();
      }

      if( $clenderecopecas->erro_status == "0" ){
        $sqlerro = true;
        $erro    = $clenderecopecas->erro_msg;
      }
  }

  $result = $clautoexec->sql_record($clautoexec->sql_query($y50_codauto));
  if($clautoexec->numrows > 0){

    $clautoexec->y15_codauto = $y50_codauto;
    $clautoexec->y15_codigo  = $y15_codigo;
    $clautoexec->y15_codi    = $y15_codi;
    $clautoexec->y15_numero  = $y15_numero;
    $clautoexec->y15_compl   = $y15_compl;
    $clautoexec->alterar($y50_codauto);
    if ($clautoexec->erro_status==0){

      $sqlerro  = true;
      $erro_msg = $clautoexec->erro_msg;
    }
  }else{
    $clautoexec->incluir($y50_codauto);
    if ($clautoexec->erro_status==0){
      $sqlerro=true;
      $erro_msg=$clautoexec->erro_msg;
    }
  }
  if ($sqlerro==false){
    $passa=true;
  }

                if($sqlerro==false){
                    $rowParagrafo = count($paragrafoTipo);

                    $pSql     = 'select * from fiscalizacao.fis_paragrafoauto where pl10_auto = '.$y50_codauto;
                    $pRs      = db_query($pSql);
                    $pRowsDel = pg_num_rows($pRs);
                    $pDel     = array();

                    if($pRowsDel > 0){
                        for($i=0; $i < pg_num_rows($pRs); $i++){
                            db_fieldsmemory($pRs, $i);
                            $pDel[] = $pl10_codigo;
                        }
                    }

                    $pArr = array();
                    for($i=0; $i < $rowParagrafo; $i++){
                        $clparagrafoauto->pl10_codigo    = '';
                        $clparagrafoauto->pl10_auto      = $y50_codauto;
                        $clparagrafoauto->pl10_paragrafo = $paragrafoTipo[$i];
                        $clparagrafoauto->pl10_texto     = addslashes($paragrafoTexto[$i]);
                        $clparagrafoauto->pl10_usu       = db_getsession('DB_id_usuario');

                        if($paragrafoCod[$i] == ''){
                            $clparagrafoauto->incluir();
                            if($clparagrafoauto->erro_status==0){
                                $sqlerro=true;
                                $erro_msg=$clparagrafoauto->erro_msg;
                                break;
                            }
                        }else{
                            $clparagrafoauto->pl10_codigo = $paragrafoCod[$i];
                            $clparagrafoauto->alterar();
                            if($clparagrafoauto->erro_status==0){
                                $sqlerro=true;
                                $erro_msg=$clparagrafoauto->erro_msg;
                                break;
                            }
                            $pArr[] = $paragrafoCod[$i];
                        }
                    }

                    if($sqlerro==false){
                        $delArr = array_diff($pDel, $pArr);

                        foreach($delArr as $key => $value) {
                            $clparagrafoauto->pl10_codigo = $value;
                            $clparagrafoauto->excluir();

                            if($clparagrafoauto->erro_status==0){
                                $sqlerro=true;
                                $erro_msg=$clparagrafoauto->erro_msg;
                                break;
                            }
                        }
                    }
                }

                if ($sqlerro==false){
                    $passa=true;
                }
            db_fim_transacao($sqlerro);
}else if(isset($chavepesquisa)){

   $db_opcao = 2;
   $result   = $clauto->sql_record($clauto->sql_query($chavepesquisa,"*",null," y50_codauto = $chavepesquisa and y50_instit = ".db_getsession('DB_instit') ));
   if ($clauto->numrows>0){
     db_fieldsmemory($result,0);
   }
   $result = $clautolocal->sql_record($clautolocal->sql_query($chavepesquisa,"*"));
   if($clautolocal->numrows > 0){
     db_fieldsmemory($result,0);
   }
   $result = $clautoexec->sql_record($clautoexec->sql_query($chavepesquisa,"fis_autoexec.* , j14_nome as j14_nome_exec,j13_descr as j13_descr_exec"));
   if($clautoexec->numrows > 0){
     db_fieldsmemory($result,0);
   }

   $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y14_numero,end01_compl as  y14_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$chavepesquisa} and end01_tipopeca = 'A' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
   $db_botao = true;

    echo "
    <script>

      parent.iframe_autolevanta.location.href      = 'fis1_fis_autolevanta001.php?y50_codauto=".$chavepesquisa."&abas=1';\n
      parent.iframe_autotipo.location.href         = 'fis1_fis_autotipo001.php?y59_codauto=".$chavepesquisa."&abas=1';\n
      // parent.iframe_receitas.location.href         = 'fis1_fis_autorec001.php?y59_codauto=".$chavepesquisa."&abas=1';\n
      parent.iframe_fiscais.location.href          = 'fis1_fis_autousu001.php?y59_codauto=".$chavepesquisa."&abas=1';\n
      parent.iframe_testem.location.href           = 'fis1_fis_autotestem001.php?y24_codauto=".$chavepesquisa."&abas=1';\n
      parent.iframe_respons.location.href          = 'fis1_fis_autorespons001.php?y124_codauto=".$chavepesquisa."&abas=1';\n
      // parent.iframe_calculo.location.href          = 'fis1_fis_autocalc001.php?y50_codauto=".$chavepesquisa."&abas=1';\n
      parent.iframe_precalculo.location.href       = 'fis1_fis_autoprecalc001.php?y50_codauto=".$chavepesquisa."&abas=1';\n

      parent.document.formaba.autolevanta.disabled = false;
      parent.document.formaba.autotipo.disabled    = false;
      // // parent.document.formaba.receitas.disabled    = false;
      parent.document.formaba.fiscais.disabled     = false;
      parent.document.formaba.testem.disabled      = false;
      parent.document.formaba.respons.disabled     = false;
      // parent.document.formaba.calculo.disabled     = false;
      parent.document.formaba.precalculo.disabled  = false;
    </script>
      ";

 $sqlprocfiscal = "select y111_procfiscal as procfiscal,z01_nome as nome
                     from fiscalizacao.fis_procfiscalauto
                          inner join fiscalizacao.fis_procfiscalcgm on y111_procfiscal = y101_procfiscal
                          inner join cgm           on y101_numcgm     = z01_numcgm
                    where y111_auto = $chavepesquisa ";
	 $resultprocfiscal = db_query($sqlprocfiscal);
	 $linhasprocfiscal = pg_num_rows($resultprocfiscal);
	 if($linhasprocfiscal>0){
	 	db_fieldsmemory($resultprocfiscal,0);
	 }else{
	 	$nome="";
	 }
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
    if ($passa==false){
      include(modification("forms/db_frm_fis_auto.php"));
    }
    ?>
  </div>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){

  if($clauto->erro_status=="0"||$sqlerro==true){

    $clauto->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clauto->erro_campo!=""){

      echo "<script> document.form1.".$clauto->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clauto->erro_campo.".focus();</script>";
    }elseif($clautoexec->erro_campo!=""){

      echo "<script> document.form1.".$clautoexec->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clautoexec->erro_campo.".focus();</script>";
    }elseif($clautolocal->erro_campo!=""){

      echo "<script> document.form1.".$clautolocal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clautolocal->erro_campo.".focus();</script>";
    }

  }else{
    $clauto->erro(true,false);
    echo "
         <script>
         function js_src(){

           parent.iframe_auto.location.href         = 'fis1_fis_auto002.php?chavepesquisa=".$clauto->y50_codauto."&abas=1';\n
           parent.iframe_autolevanta.location.href  = 'fis1_fis_autolevanta001.php?y50_codauto=".$clauto->y50_codauto."&abas=1';\n
           parent.iframe_autotipo.location.href     = 'fis1_fis_autotipo001.php?y59_codauto=".$clauto->y50_codauto."&abas=1';\n
           // parent.iframe_receitas.location.href     = 'fis1_fis_autorec002.php?y59_codauto=".$clauto->y50_codauto."&abas=1';\n
           parent.iframe_fiscais.location.href      = 'fis1_fis_autousu001.php?y59_codauto=".$clauto->y50_codauto."&abas=1';\n
           parent.iframe_testem.location.href       = 'fis1_fis_autotestem001.php?y24_codauto=".$clauto->y50_codauto."&abas=1';\n
           parent.iframe_respons.location.href      = 'fis1_fis_autorespons001.php?y124_codauto=".$clauto->y50_codauto."&abas=1';\n
           // parent.iframe_calculo.location.href      = 'fis1_fis_autocalc001.php?y50_codauto=".$clauto->y50_codauto."&abas=1';\n
           parent.iframe_precalculo.location.href   = 'fis1_fis_autoprecalc001.php?y50_codauto=".$clauto->y50_codauto."&abas=1';\n

           parent.mo_camada('autolevanta');

            parent.document.formaba.autolevanta.disabled = false;
            parent.document.formaba.autotipo.disabled    = false;
            // // parent.document.formaba.receitas.disabled    = false;
            parent.document.formaba.fiscais.disabled     = false;
            parent.document.formaba.testem.disabled      = false;
            parent.document.formaba.respons.disabled     = false;
            // parent.document.formaba.calculo.disabled     = false;
            parent.document.formaba.precalculo.disabled  = false;
         }
         js_src();
         </script>
       ";
  }
}

if($db_opcao==22){
  echo "<script>
    document.form1.pesquisar.click();
    </script>";
}
?>
