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
include(modification("classes/db_fis_fiscal_classe.php"));
include(modification("classes/db_fis_fiscalocal_classe.php"));
include(modification("classes/db_fis_fiscexec_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_fis_procfiscalnotificacao_classe.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
// -------------
if(!isset($abas)){
  echo "<script>location.href='fis1_fis_fiscal005.php?db_opcao=2$getIntimacao'</script>";
  exit;
}

$clenderecopecas = db_utils::getDao("fis_enderecopecas");
$tipoEnd     = ((isset($intimacao) && $intimacao == 1) ? 'I' : 'N');
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clfiscal     = new cl_fis_fiscal;
$clfiscalocal = new cl_fis_fiscalocal;
$clfiscexec   = new cl_fis_fiscexec;
$clparagrafointimacao  = new cl_fis_fiscalparagrafointimacao;
$clprocfiscalnotificacao = new cl_fis_procfiscalnotificacao;

$db_opcao = 22;
$db_botao = false;
echo "
<script>
  parent.document.formaba.fiscaltipo.disabled=true;
  // parent.document.formaba.receitas.disabled=true;
  parent.document.formaba.fiscais.disabled=true;
  parent.document.formaba.test.disabled=true;
  // parent.document.formaba.artigos.disabled=true;
</script>
";
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  $clfiscal->alterar($y30_codnoti);
  
  if($y12_codigo != ""){

    $result = $clfiscalocal->sql_record($clfiscalocal->sql_query($y30_codnoti));
    if($clfiscalocal->numrows > 0){
      $clfiscalocal->y12_codnoti=$y30_codnoti;
      $clfiscalocal->y12_codigo=$y12_codigo;
      $clfiscalocal->y12_codi=$y12_codi;
      $clfiscalocal->y12_numero=$y12_numero;
      $clfiscalocal->y12_compl=$y12_compl;
      $clfiscalocal->alterar($y30_codnoti);
    }else{
      $clfiscalocal->incluir($y30_codnoti);
    }
  }else{

      $clenderecopecas->end01_codpeca  = $y30_codnoti;
      $clenderecopecas->end01_tipopeca = $tipoEnd;
      $clenderecopecas->end01_rua      = $j14_nome;
      $clenderecopecas->end01_numero   = $y12_numero;
      $clenderecopecas->end01_compl    = $y12_compl;
      $clenderecopecas->end01_bairro   = $j13_descr;

      $rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$y30_codnoti} and end01_tipopeca = '$tipoEnd'");
      
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
  $result = $clfiscexec->sql_record($clfiscexec->sql_query($y30_codnoti));
  if($clfiscexec->numrows > 0){
    $clfiscexec->y13_codnoti=$y30_codnoti;
    $clfiscexec->y13_codigo=$y13_codigo;
    $clfiscexec->y13_codi=$y13_codi;
    $clfiscexec->y13_numero=$y13_numero;
    $clfiscexec->y13_compl=$y13_compl;
    $clfiscexec->alterar($y30_codnoti);
  }else{
    $clfiscexec->incluir($y30_codnoti);
  }


// exclui e inclui no procfiscalnotificacao
	 $sqlprocfiscalv = "select y110_sequencial from fiscalizacao.fis_procfiscalnotificacao where y110_notificacaofiscal = $y30_codnoti ";
	 $resultprocfiscalv = pg_query($sqlprocfiscalv);
	 $linhasprocfiscalv = pg_num_rows($resultprocfiscalv);
	 if($linhasprocfiscalv>0){
	 	 db_fieldsmemory($resultprocfiscalv,0);
	   $clprocfiscalnotificacao->y110_sequencial =$y110_sequencial;
		 $clprocfiscalnotificacao->excluir($y110_sequencial);
		 if($clprocfiscalnotificacao->erro_status==0){
				$erro=$clprocfiscalnotificacao->erro_msg;
	      $sqlerro = true;
		 }
	 }
	 if($procfiscal!=""){
		  $clprocfiscalnotificacao->y110_notificacaofiscal = $y30_codnoti;
		  $clprocfiscalnotificacao->y110_procfiscal        = $procfiscal ;
		  $clprocfiscalnotificacao->incluir(null);
			if($clprocfiscalnotificacao->erro_status==0){
				$erro=$clprocfiscalnotificacao->erro_msg;
	      $sqlerro = true;
	    }
		}

  
                if($sqlerro==false){
                    $rowParagrafo = count($paragrafoTipo);

                    $pSql     = 'select * from fiscalizacao.fis_paragrafointimacao where pl11_intimacao = '.$y30_codnoti;
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
                        $clparagrafointimacao->pl11_codigo    = '';
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
                            $clparagrafointimacao->pl11_codigo = $paragrafoCod[$i];
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

                if ($sqlerro==false){
                    $passa=true;
                }
            db_fim_transacao($sqlerro);
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clfiscal->sql_record($clfiscal->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);
   $result = $clfiscalocal->sql_record($clfiscalocal->sql_query($chavepesquisa,"*"));
   if($clfiscalocal->numrows > 0){
     db_fieldsmemory($result,0);
   }else{
   		$result_ender=$clfiscal->sql_record($clfiscal->sql_query_ender($chavepesquisa));
   		if ($clfiscal->numrows>0){
   			     db_fieldsmemory($result_ender,0);
   			     $y12_codigo=$codrua;
   			     $y12_codi=$codbairro;
   			     $j14_nome=$nomerua;
   			     $j13_descr=$nomebairro;
   			     $y12_compl=$compl;
   			     $y12_numero=$numero;

   		}
   }
   $result = $clfiscexec->sql_record($clfiscexec->sql_query($chavepesquisa,"*"));
   if($clfiscexec->numrows > 0){
     db_fieldsmemory($result,0);
     $j14_nome_exec = $j14_nome;
   }
   $db_botao = true;
echo "
<script>
  parent.iframe_fiscaltipo.location.href='fis1_fis_fiscaltipo001.php?y31_codnoti=".$chavepesquisa."&abas=1".$getIntimacao."';\n
  // parent.iframe_receitas.location.href='fis1_fis_fiscalrec001.php?y42_codnoti=".$chavepesquisa."&abas=1".$getIntimacao."';\n
  parent.iframe_fiscais.location.href='fis1_fis_fiscalusuario001.php?y38_codnoti=".$chavepesquisa."&abas=1".$getIntimacao."';\n
  parent.iframe_test.location.href='fis1_fis_fisctestem001.php?y23_codnoti=".$chavepesquisa."&abas=1".$getIntimacao."';\n
  // parent.iframe_artigos.location.href='fis1_fis_fiscarquivos001.php?y26_codnoti=".$chavepesquisa."&abas=1".$getIntimacao."';\n
  parent.document.formaba.fiscaltipo.disabled=false;
  // parent.document.formaba.receitas.disabled=false;
  parent.document.formaba.fiscais.disabled=false;
  parent.document.formaba.test.disabled=false;
  // parent.document.formaba.artigos.disabled=false;
</script>
  ";

$sqlprocfiscal = " select y110_procfiscal as procfiscal,z01_nome as nome
                     from fiscalizacao.fis_procfiscalnotificacao
                     inner join fiscalizacao.fis_procfiscalcgm on y110_procfiscal = y101_procfiscal
										 inner join cgm           on y101_numcgm     = z01_numcgm
  								 where y110_notificacaofiscal = $chavepesquisa
									";
	 $resultprocfiscal = pg_query($sqlprocfiscal);
	 $linhasprocfiscal = pg_num_rows($resultprocfiscal);
	 if($linhasprocfiscal>0){
	 	 db_fieldsmemory($resultprocfiscal,0);
	 }else{
	 	 $nome="";
	 }

   $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y12_numero,end01_compl as  y12_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$chavepesquisa} and end01_tipopeca = '{$tipoEnd}' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }

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
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($clfiscal->erro_status=="0"){
    $clfiscal->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clfiscal->erro_campo!=""){
      echo "<script> document.form1.".$clfiscal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfiscal->erro_campo.".focus();</script>";
    };
  }else{
    $clfiscal->erro(true,false);
    echo "
         <script>
         function js_src(){
           parent.iframe_fiscal.location.href='fis1_fis_fiscal002.php?chavepesquisa=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.iframe_fiscaltipo.location.href='fis1_fis_fiscaltipo001.php?y31_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           // parent.iframe_receitas.location.href='fis1_fis_fiscalrec002.php?chavepesquisa=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.iframe_fiscais.location.href='fis1_fis_fiscalusuario001.php?y38_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.iframe_test.location.href='fis1_fis_fisctestem001.php?y23_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           // parent.iframe_artigos.location.href='fis1_fis_fiscarquivos001.php?y26_codnoti=".$clfiscal->y30_codnoti."&abas=1".$getIntimacao."';\n
           parent.mo_camada('fiscaltipo');
	   parent.document.formaba.fiscaltipo.disabled=false;
	   // parent.document.formaba.receitas.disabled=true;
	   parent.document.formaba.fiscais.disabled=false;
	   parent.document.formaba.test.disabled=false;
         }
         js_src();
         </script>
       ";
  };
};
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
