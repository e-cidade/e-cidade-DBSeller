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

include(modification("libs/db_conecta.php"));
include(modification("libs/db_stdlib.php"));
include(modification("libs/db_sql.php"));
session_start();
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
$result = db_query("SELECT distinct m_publico,m_arquivo,m_descricao
                       FROM db_menupref
		       WHERE m_arquivo = 'digitaissqn.php'
		       ORDER BY m_descricao
		       ");
db_fieldsmemory($result,0);
if($m_publico != 't'){
  if(!session_is_registered("DB_acesso"))
    echo"<script>location.href='index.php?".base64_encode('erroscripts=3')."'</script>";
}
mens_help();
db_mensagem("issqnretencao_cab","issqnretencao_rod");
$dblink="index.php";
$db_verificaip = db_verifica_ip();
if($db_verificaip == "0"){
  $onsubmit = "";
}else{
  $onsubmit = "";
}

?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/db_script.js"></script>
<script>
function js_vericampos(){
  jmes=document.form1.mes.value;
  if(jmes=="mes"){
    alert("Favor selecionar o mês!");
    return false
  }


  inscricaow = document.form1.inscricaow.value;
  cgc = document.form1.cgc.value;

  expReg = "/[- /.]/g";

  var cgc = new Number(cgc.replace(expReg,""));
  var inscricaow = new Number(inscricaow.replace(expReg,""));

  if(inscricaow==""&&cgc==""){
    alert("Favor preencher um dos campos de identificação!");
    document.form1.inscricaow.focus();
    return false
  }
  if(isNaN(inscricaow)){
     alert("Verifique o campo Inscricão!");
     return false
  }

}
</script>
<style type="text/css">
<?php db_estilosite();
?>
</style>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>" onLoad="" <?php mens_OnHelp() ?>>
<?php
mens_div();
?>
<center>
<table width="766" border="0" cellpadding="0" cellspacing="0" bgcolor="<?$w01_corbody?>">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" align="left" valign="top"><img src="imagens/cabecalho.jpg"></td>
</tr>
      </table></td>
  </tr>
  <tr>
    <td>
      <table class="bordas" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td nowrap width="90%">
            &nbsp;<a href="index.php" class="links">Principal &gt;</a>
          </td>
	  <td align="center" width="10%" onClick="MM_showHideLayers('<?=$nome_help?>','',(document.getElementById('<?=$nome_help?>').style.visibility == 'visible'?'hide':'show'));">
	    <a href="#" class="links">Ajuda</a>
          </td>
       </tr>
     </table>
   </td>
  </tr>
  <tr>
    <td align="left" valign="top">
	  <table width="100%" height="313" border="0" cellpadding="0" cellspacing="0">
      <tr>
            <td width="90" align="left" valign="top">
          <?php    db_montamenus();
          ?>
		</td>
            <td align="left" valign="top">
              <!-- CORPO -->

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="60" align="<?=$DB_align1?>">
                    <?=$DB_mens1?>
                  </td>
                </tr>
                <tr>
                  <td height="200" align="center" valign="middle">
  <form name="form1" method="post" action="opcoesissqn.php">
 <table height="100%" border="0" cellpadding="0" cellspacing="0" background="imagens/azul_ceu_O.jpg">
  <input name="primeiravez" type="hidden" value="true">
    <tr>
      <td valign="top">
       <table width="100%" height="223">
          <tr>
            <td height="148" valign="top">
              <p align="center">
              <table width="100%">
                <tr>
                  <td nowrap width="48%" align="right">Inscri&ccedil;&atilde;o Alvar&aacute;:</td>
                  <td width="52%"><input name="inscricaow" type="text" class="digitacgccpf" size="8" maxlength="6"> </td>

                </tr>
                <tr>
                  <td><input name="cgc" type="hidden" class="digitacgccpf" id="cgc" size="18" maxlength="18" onKeyDown="FormataCNPJ(this,event)"></td>
                </tr>
                <tr>
                  <td align="right">Compet&ecirc;ncia:</td>
                  <td colspan="2" nowrap>
                     <script>
		     function js_criames(obj){
                       for(i=1;i<document.form1.mes.length;i){
		         document.form1.mes.options[i] = null;
                       }
		       var dth = new Date(<?=date("Y")?>,<?=date("m")?>,'1');
 		       if(document.form1.ano.options[0].value != obj.value ){
	                 for(j=1;j<13;j++){
			   var dt = new Date('<?=date("Y")?>',j,'1');
		           document.form1.mes.options[j] = new Option(db_mes(j),dt.getMonth());
                         }
 		       }else{
			 data = (dth.getMonth() == 0?12:dth.getMonth());
	                 for(j=1;j<data+1;j++){
			   var dt = new Date('<?=date("Y")?>',j,'1');
		           document.form1.mes.options[j] = new Option(db_mes(j),dt.getMonth());
                         }
		       }
                     }
		     </script>
                    <select name="ano">
                    <?php
		      $sano = date("Y");
                      for($ci = $sano; $ci >= ($sano-10); $ci--){
                        echo "<option value=".$ci." >$ci</option>";
                      }
                    ?>
		    </select>
                    <select name="mes" id="mes">
                    <?php
		      $smes = 1;
                      for($ci = 1; $ci <= 12; $ci++){
                        echo '"<option value="'.$ci.'"' . ($ci == date("m")?"selected":"") . ' >'.$ci.'</option>"';
                      }
                    ?>
                  </td>
              </tr>
              <tr>
              </table>
                  <input name="first" type="hidden">
              <p align="center">
                <input class="botao" type="submit" name="pesquisa" value="Pesquisa"  onclick="return js_vericampos()">
              </p>
            </td>
          </tr>


		   		  <!-- InstanceEndEditable -->
                  </td>
                </tr>
                <tr>
                  <td height="60" align="<?=$DB_align2?>">
                    <?=$DB_mens2?>
                  </td>
                </tr>
              </table>
            </td>
         </tr>
      </table>
    </td>
  </tr>
</table>
</center>
<?php
db_rodape();
?>
</body>
</html>
<?php
db_logs("","",0,"Digita Codigo da Inscricao para o issqn retencao.");
if(isset($erroscripts)){
  echo "<script>alert('".$erroscripts."');</script>";
}
?>
