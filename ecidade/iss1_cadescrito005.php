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
include(modification("dbforms/db_funcoes.php"));
$clcadescrito = new cl_cadescrito;
$clescrito = new cl_escrito;

db_postmemory($HTTP_POST_VARS);
   $db_opcao = 22;
$db_botao = false;
if(isset($alterar)){
  $sqlerro=false;
  db_inicio_transacao();
  $clcadescrito->q86_datalimite = $q86_datalimite;
  $clcadescrito->alterar($q86_numcgm);
  if($clcadescrito->erro_status==0){
    $sqlerro=true;
  }
  $sMsgErroAux = "";
  if ($sqlerro == false) {
     if (empty($q86_datalimite)) {
       $sDataFim = 'null';
     } else {
       $sDataFim  = substr($q86_datalimite,6,4)."-".substr($q86_datalimite,3,2)."-".substr($q86_datalimite,0,2);
     }
     $sWhere = " q10_numcgm = {$q86_numcgm}";
     $resultCli = $clescrito->sql_record($clescrito->sql_query_file(null, 'q10_sequencial', null, $sWhere));
     $totCli=$clescrito->numrows;
     if ($totCli > 0) {
        for($i=0; $i<$totCli; $i++){
          if ($sqlerro == false){
            db_fieldsmemory($resultCli,$i);
            $clescrito->q10_sequencial = $q10_sequencial;
            $clescrito->q10_dtfim = $sDataFim;
            if ($sDataFim != 'null') {
              $clescrito->alterar($q10_sequencial);
            } else {
              $clescrito->alterar_nulos($q10_sequencial);
            }
            if($clescrito->erro_status==0){
              $sqlerro=true;
              $sMsgErroAux = $clescrito->erro_msg;
            }
          }
        }       
     }
  }
  $erro_msg = $clcadescrito->erro_msg." ".$sMsgErroAux;
  db_fim_transacao($sqlerro);
   $db_opcao = 2;
   $db_botao = true;
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $db_botao = true;
   $result = $clcadescrito->sql_record($clcadescrito->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="center" valign="top" bgcolor="#CCCCCC">
    <center>
  	<?php
  	include(modification("forms/db_frmcadescrito.php"));
  	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php
if(isset($alterar)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($clcadescrito->erro_campo!=""){
      echo "<script> document.form1.".$clcadescrito->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clcadescrito->erro_campo.".focus();</script>";
    };
  }else{
   db_msgbox($erro_msg);
  }
}
if(isset($chavepesquisa)){
 echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.cadescritoresp.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_cadescritoresp.location.href='iss1_cadescritoresp001.php?q84_cadescrito=".@$q86_numcgm."';
     ";
         if(isset($liberaaba)){
           echo "  parent.mo_camada('cadescritoresp');";
         }
 echo"}\n
    js_db_libera();
  </script>\n
 ";
}
 if($db_opcao==22||$db_opcao==33){
    echo "<script>document.form1.pesquisar.click();</script>";
 }
?>