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
include(modification("classes/db_cgm_classe.php"));
db_postmemory($HTTP_POST_VARS);
if(!isset($pesquisar))
  parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clcgm = new cl_cgm;
$clrotulo = new rotulocampo;
$clcgm->rotulo->label("z01_numcgm");
$clcgm->rotulo->label("z01_nome");
$clcgm->rotulo->label("z01_cgccpf");
$clrotulo->label("DBtxt30");
$clrotulo->label("DBtxt31");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
  function js_submit_numcgm_buscanome(numcgm){
    document.form_busca_dados.numcgm_busca_dados.value = numcgm;
    document.form_busca_dados.submit();
  }
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr> 
    <td height="63" align="center" valign="top">
       <table width="100%" border="0" cellspacing="0">
	<form name="form2" method="post" action="" >
        <tr> 
          <td align="right">C&oacute;digo: </td>
          <td >
		  <!--input name="numcgmDigitadoParaPesquisa" type="text" id="numcgmDigitadoParaPesquisa" value="<?php if (isset($numcgmDigitadoParaPesquisa)){echo $numcgmDigitadoParaPesquisa;} ?>" size="10" maxlength="6"-->
          <?php
		  db_input('z01_numcgm',6,$Iz01_numcgm,true,'text',4,"","numcgmDigitadoParaPesquisa");
		  ?>
		  </td>
          <td align="right">&nbsp;<?=$DBtxt30?>: </td>
          <td>
		  <!--input name="nomeDigitadoParaPesquisa" type="text" id="nomeDigitadoParaPesquisa4" value="<?php if (isset($nomeDigitadoParaPesquisa)){echo $nomeDigitadoParaPesquisa;} ?>" size="41" maxlength="40"-->
		  <?php
		  db_input('z01_cgccpf',20,$Iz01_cgccpf,true,'text',1,"",'cpf');
		  ?>
          </td>
        
        </tr>
        <tr> 
          <td align="right">&nbsp;Nome: </td>
          <td>
		  <!--input name="nomeDigitadoParaPesquisa" type="text" id="nomeDigitadoParaPesquisa4" value="<?php if (isset($nomeDigitadoParaPesquisa)){echo $nomeDigitadoParaPesquisa;} ?>" size="41" maxlength="40"-->
		  <?php
		  db_input('z01_nome',40,$Iz01_nome,true,'text',4,"",'nomeDigitadoParaPesquisa');
		  ?>
          </td>
          <td align="right">&nbsp;<?=$DBtxt31?>: </td>
          <td>
		  <!--input name="nomeDigitadoParaPesquisa" type="text" id="nomeDigitadoParaPesquisa4" value="<?php if (isset($nomeDigitadoParaPesquisa)){echo $nomeDigitadoParaPesquisa;} ?>" size="41" maxlength="40"-->
		  <?php
		  db_input('z01_cgccpf',20,$Iz01_cgccpf,true,'text',1,"",'cnpj');
		  ?>
          </td>
        
        
		</tr>
        <tr> 
		
          <td colspan="4" align="center"><br>
		    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
            <input name="limpar" type="button" id="naoencontrado2" value="Limpar" onClick="js_limpa()">
            <input name="Fechar" type="button" id="limpar" value="Fechar" onClick="parent.func_nome.hide();"></td>
        </tr>
		</form>
      </table> 
</td>
<script>
function js_limpa(){
  for(i =0;i < document.form2.elements.length;i++){
    if(document.form2.elements[i].type == 'text'){
      document.form2.elements[i].value = "";
    }
  }
}
</script>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php
if(!isset($pesquisa_chave)){

   echo "<script>
         js_limpa();
         document.form2.nomeDigitadoParaPesquisa.focus();
         </script>";


  if(isset($campos)==false){
    $campos = "
      cgm.z01_numcgm, z01_nome, z01_ender, z01_munic, z01_uf, z01_cep, z01_email
    ";
  }
  $clnome = new cl_cgm;
  if (isset($nomeDigitadoParaPesquisa) && ($nomeDigitadoParaPesquisa!="") ){
	$nomeDigitadoParaPesquisa = strtoupper($nomeDigitadoParaPesquisa);
	$sql = $clnome->sqlnome($nomeDigitadoParaPesquisa,$campos);
  }else if(isset($numcgmDigitadoParaPesquisa) && $numcgmDigitadoParaPesquisa != ""){
    $sql = $clnome->sql_query($numcgmDigitadoParaPesquisa,$campos);
  }else if(isset($cpf) && $cpf != ""){
    $sql = $clnome->sql_query("",$campos,""," z01_cgccpf = '$cpf' ");
  }else if(isset($cnpj) && $cnpj != ""){
    $sql = $clnome->sql_query("",$campos,""," z01_cgccpf = '$cnpj' ");
  }else{
	$sql = "";    
  }
  db_lovrot($sql,14,"()","",$funcao_js);
}else{
   if($pesquisa_chave!=""){
     $result = $clcgm->sql_record($clcgm->sql_query($pesquisa_chave));
     if(($result!=false) && (pg_numrows($result) != 0)){
        db_fieldsmemory($result,0);
        echo "<script>".$funcao_js."(false,\"$z01_nome\");</script>";
     }else{
        echo "<script>".$funcao_js."(true,'Código (".$z01_numcgm.") não Encontrado');</script>";
     }
   }
}
  ?>
    </td>
  </tr>
</table>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
