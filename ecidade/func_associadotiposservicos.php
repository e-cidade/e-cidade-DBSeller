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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$classociadotiposservicos = new cl_associadotiposservicos();
$classociadotiposservicos->rotulo->label('fm09_codigo');
$classociadotiposservicos->rotulo->label('fm09_descricao');

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="estilos.css">
    <script src="scripts/scripts.js"></script>
</head>
<body>
<form name="form2" method="post" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
            <tr>
                <td><label for="chave_fm09_codigo"><?=$Lfm09_codigo?></label></td>
                <td><?php db_input("fm09_codigo", $Mfm09_codigo, $Ifm09_codigo, true, "text", 4, "", "chave_fm09_codigo"); ?></td>
            </tr>
            <tr>
                <td><label for="chave_fm09_descricao"><?=$Lfm09_descricao?></label></td>
                <td><?php db_input("fm09_descricao", $Mfm09_descricao, $Ifm09_descricao, true, "text", 4, "", "chave_fm09_descricao"); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_associadotiposservicos.hide();">
</form>
<?php
if (isset($pesquisa_chave) === false) {
    if (isset($campos) === false) {
        if (file_exists("funcoes/db_func_associadotiposservicos.php") === true) {
            include(modification("funcoes/db_func_associadotiposservicos.php"));
        } else {
            $campos = "associadotiposservicos.*";
        }
    }
        if(isset($chave_fm09_codigo) && (trim($chave_fm09_codigo)!="") ){
	         $sql = $classociadotiposservicos->sql_query($chave_fm09_codigo,$campos,"fm09_codigo");
        }else if(isset($chave_fm09_descricao) && (trim($chave_fm09_descricao)!="") ){
	         $sql = $classociadotiposservicos->sql_query("",$campos,"fm09_descricao"," fm09_descricao like '$chave_fm09_descricao%' ");
        }else{
           $sql = $classociadotiposservicos->sql_query("",$campos,"fm09_codigo","");
        }
        $repassa = array();
        if(isset($chave_fm09_descricao)){
          $repassa = array("chave_fm09_codigo"=>$chave_fm09_codigo,"chave_fm09_descricao"=>$chave_fm09_descricao);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      } else {
        if ($pesquisa_chave != null && $pesquisa_chave != "") {
          $result = $classociadotiposservicos->sql_record($classociadotiposservicos->sql_query($pesquisa_chave));
          if($classociadotiposservicos->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$fm09_descricao',false);</script>";
        } else {
            echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
        }
    } else {
        echo "<script>{$funcao_js}('', false);</script>";
    }
}
?>
</body>
</html>
<?php if (isset($pesquisa_chave) === false) { ?>
    <script rel="script" type="text/javascript">
    </script>
<?php } ?>
<script rel="script" type="text/javascript">
    js_tabulacaoforms("form2","chave_fm09_descricao",true,1,"chave_fm09_descricao",true);
</script>
