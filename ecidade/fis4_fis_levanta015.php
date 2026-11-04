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
require_once(modification("classes/db_fis_levanta_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_procfiscallevanta_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$cllevanta           = new cl_fis_levanta;
$clprocfiscallevanta = new cl_fis_procfiscallevanta;
$db_opcao = 22;
$db_botao = false;

if(isset($alterar)){

  db_inicio_transacao();
  $db_opcao = 2;
  $db_botao = true;
  $sqlerro  = false;
  
  $sAnoIni  = $y60_dtini_ano;
  $sAnoFim  = $y60_dtfim_ano;
  $sMesInic = $y60_dtini_mes;
  $sMesFim  = $y60_dtfim_mes;
  $sSqlCompetencias  = " select * from fiscalizacao.fis_levvalor where y63_codlev = $y60_codlev and  y63_ano <= $sAnoIni and y63_mes < $sMesInic"; 
  $sSqlCompetencias2 = " select * from fiscalizacao.fis_levvalor where y63_codlev = $y60_codlev and  y63_ano >= $sAnoFim and y63_mes > $sMesFim "; 
  $rsCompetencias  = db_query($sSqlCompetencias);
  $rsCompetencias2 = db_query($sSqlCompetencias2);

  if( pg_num_rows( $rsCompetencias ) > 0 || pg_num_rows( $rsCompetencias2 ) ){
    $erro_msg = "Já existem lançamentos fora do período digitado, Alteração não efetuada"; 
    $sqlerro  = true;
  }   

  if($sqlerro == false){

    $cllevanta->alterar($y60_codlev);
    $erro_msg= $cllevanta->erro_msg;

    $sqlprocfiscalv    = "select y112_sequencial from fiscalizacao.fis_procfiscallevanta where y112_levanta = $y60_codlev";
    $resultprocfiscalv = db_query($sqlprocfiscalv);
    $linhasprocfiscalv = pg_num_rows($resultprocfiscalv);
    if($linhasprocfiscalv>0){

     db_fieldsmemory($resultprocfiscalv,0);
     $clprocfiscallevanta->y112_sequencial = $y112_sequencial;
     $clprocfiscallevanta->excluir($y112_sequencial);
     if($clprocfiscallevanta->erro_status==0){

        $erro=$clprocfiscallevanta->erro_msg;
        $sqlerro = true;
     }
    }
  }
  if($sqlerro == false){
  	if($procfiscal!=""){

			$clprocfiscallevanta->y112_procfiscal = $procfiscal;
			$clprocfiscallevanta->y112_levanta    = $y60_codlev;
			$clprocfiscallevanta->incluir(null);
			if($clprocfiscallevanta->erro_status==0){

				$erro=$clprocfiscallevanta->erro_msg;
			  $sqlerro = true;
			}
  	}
  }
  db_fim_transacao();
}else if(isset($chavepesquisa)){

   $db_opcao = 2;
   $result = $cllevanta->sql_record($cllevanta->sql_query_inf($chavepesquisa));

   db_fieldsmemory($result,0);

   if($y60_importado == 't'){
      $nops = true;
      $db_botao = false;
   }else{
     $db_botao = true;
   }

   $sqlprocfiscal = "select y112_procfiscal as procfiscal,z01_nome as nome
	                     from fiscalizacao.fis_procfiscallevanta
	                          inner join fiscalizacao.fis_procfiscalcgm on y112_procfiscal = y101_procfiscal
	                          inner join cgm on y101_numcgm=z01_numcgm
	                    where y112_levanta = $chavepesquisa";
	 $resultprocfiscal = db_query($sqlprocfiscal);
	 $linhasprocfiscal = pg_num_rows($resultprocfiscal);
	 if($linhasprocfiscal>0){
	 	db_fieldsmemory($resultprocfiscal,0);
	 }else{
	 	$nome="";
	 }
  $sqlProc = "select 
                p58_numero || '/' || p58_ano as p58_numero, 
                p58_codproc as y60_proces, 
                p58_requer
              from protprocesso where  p58_codproc = $y60_proces ";
  $rsProc = db_query($sqlProc);
  if(pg_num_rows($rsProc) > 0){
    db_fieldsmemory($rsProc, 0);
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
<body>
<table width="790">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
  	<?php
  	 if(isset($nops)){
  	   echo "<h4 style='color:#FF0000;'>Já Exportado!</h4>";
  	 }

  	 include(modification("forms/db_frm_fis_levanta.php"));

  	 if(isset($nops)){
  		db_msgbox("Levantamento já Exportado!!");
  	 }
  	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php 
if(isset($alterar)){
  if($cllevanta->erro_status=="0"){
    $cllevanta->erro(true,false);
    if($cllevanta->erro_campo!=""){
      echo "<script> document.form1.".$cllevanta->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllevanta->erro_campo.".focus();</script>";
    }
  }else{

     echo "
           <script>
              function js_xz(){
                 (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levvalor.document.form1.y60_contato.value='$y60_contato';\n
                 (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levusu.document.form1.y60_contato.value='$y60_contato';\n
	      }
	      js_xz();
      </script>
    ";
    $query='';
     if(isset($nops)){
          $query ="db_opcaoal=true&";
     }
     echo "<script>(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levvalor.location.href='fis1_fis_levvalor001.php?".$query."y60_contato=$y60_contato&y63_codlev=$y60_codlev';\n</script>";
    db_msgbox($erro_msg);
  }
}else if(isset($chavepesquisa)){
    $query='';
     if(isset($nops)){
          $query ="db_opcaoal=true&";
     }

     echo "
           <script>
              function js_xy(){
                parent.document.formaba.levvalor.disabled=false;\n
                parent.document.formaba.levusu.disabled=false;\n
		(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levvalor.location.href='fis1_fis_levvalor001.php?".$query."y60_contato=$y60_contato&y63_codlev=$y60_codlev';\n
		(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levusu.location.href='fis1_fis_levusu001.php?".$query."y60_contato=$y60_contato&y61_codlev=$y60_codlev';\n";
		if(empty($alterando) && empty($excluindo)){
                     echo "parent.mo_camada('levvalor');";
	        }
     echo"
              }
              js_xy();
           </script>
         ";

}
?>
