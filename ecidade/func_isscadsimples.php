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
include(modification("classes/db_isscadsimples_classe.php"));
$mostra  = "T";
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clisscadsimples = new cl_isscadsimples;
$clisscadsimples->rotulo->label("q38_sequencial");
$clisscadsimples->rotulo->label("q38_inscr");
$clisscadsimples->rotulo->label("z01_nome");
$clrotulo = new rotulocampo;
$clrotulo->label("DBtxt30");
$clrotulo->label("DBtxt31");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr> 
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tq38_sequencial?>">
              <?=$Lq38_sequencial?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("q38_sequencial",10,$Iq38_sequencial,true,"text",4,"","chave_q38_sequencial");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tq38_inscr?>">
              <?=$Lq38_inscr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("z01_nome",10,'',true,"text",4,"","chave_q38_inscr");
		       ?>
            </td>
          </tr>

          <tr>
						<td align="right">
            <label for="cpf"><?=$DBtxt30?>:</label>
						</td>
						<td>
							<?php
								db_input('z01_cgccpf',20,$Iz01_cgccpf,true,'text',1,"onkeyup='js_ValidaCampos(this,1,\"CPF\",\"\",\"\",event);'",'cpf');
							?>
						</td>
					</tr>
          <tr>
						<td align="right">
            <label for="cnpj"><?=$DBtxt31?>:</label>
						</td>
						<td>
							<?php
								db_input('z01_cgccpf',20,$Iz01_cgccpf,true,'text',1,"onkeyup='js_ValidaCampos(this,1,\"CNPJ\",\"\",\"\",event);'",'cnpj');
							?>
						</td>
					</tr>

					<?php

			    if (!isset($_GET["sbaixa"])){
          ?>
          <tr> 
            <td width="4%" align="right" nowrap title="Mostrar">
              <strong>Mostrar:</strong>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
							  $itens = array("T" => "Todos", "B" => "Baixados","A" => "Ativos");
								db_select("mostra",$itens,true,1);

		          ?>
            </td>
          </tr>
					<?php
            }//fim do if baixa.
					?>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_isscadsimples.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php
			$where = '';
			if ($mostra == "T"){

          $where = "";

			}else if ($mostra == "B"){

        $where = "q39_sequencial is not null and";
			
			}else if ($mostra == "A"){

        $where = "q39_sequencial is  null and";
			}
			if (isset($_GET["sbaixa"]) and $_GET["sbaixa"] != ''){

          $where = "q39_sequencial is null and ";

			}
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_isscadsimples.php")==true){
             include(modification("funcoes/db_func_isscadsimples.php"));
           }else{
           $campos = "isscadsimples.*,z01_nome,q39_dtbaixa";
           }
        }
        if(isset($chave_q38_sequencial) && (trim($chave_q38_sequencial)!="") ){
	         $sql = $clisscadsimples->sql_query_baixa($chave_q38_sequencial,$campos,"q38_sequencial");
        }else if(isset($chave_q38_inscr) && (trim($chave_q38_inscr)!="") ){
	         $sql = $clisscadsimples->sql_query_baixa("",$campos,"q38_inscr","$where q38_inscr like '$chave_q38_inscr%' ");
        }else if(isset($cpf) && (trim($cpf)!="") )  {
          $sql = $clisscadsimples->sql_query_baixa("",$campos,"z01_cgccpf"," z01_cgccpf = '{$cpf}' ");
        }else if(isset($cnpj) && (trim($cnpj)!="") ) {
          $sql = $clisscadsimples->sql_query_baixa("",$campos,"z01_cgccpf"," z01_cgccpf = '{$cnpj}' ");
        }else{
           $sql = $clisscadsimples->sql_query_baixa("",$campos,"q38_sequencial","$where 1=1");
        }
        $repassa = array();
        if(isset($chave_q38_inscr)){
          $repassa = array("chave_q38_sequencial"=>$chave_q38_sequencial," $where chave_q38_inscr"=>$chave_q38_inscr);
        }
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clisscadsimples->sql_record($clisscadsimples->sql_query_baixa($pesquisa_chave));
          if($clisscadsimples->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$z01_nome',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </td>
   </tr>:
</table>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
  ?>
  <script>
  </script>
  <?php
}
?>
<script>
js_tabulacaoforms("form2","chave_q38_inscr",true,1,"chave_q38_inscr",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
