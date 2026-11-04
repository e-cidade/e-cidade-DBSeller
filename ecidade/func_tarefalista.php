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
include(modification("classes/db_tarefa_classe.php"));

db_postmemory($_POST);
db_postmemory($_GET);

$cltarefa = new cl_tarefa;
$cltarefa->rotulo->label("at40_sequencial");
$cltarefa->rotulo->label("at40_descr");


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
            <td width="4%" align="right" nowrap title="<?=$Tat40_sequencial?>">
              <?=$Lat40_sequencial?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		            db_input("at40_sequencial",10,$Iat40_sequencial,true,"text",4,"","chave_at40_sequencial");
		          ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tat40_descr?>">
              <?=$Lat40_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		            db_input("chave_at40_descr",40,"",true,"text",4);
		          ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_tarefa.hide();">
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
       
      <?php

        if ( isset($lista) &&  $lista == 'false' ) {
        	$where = " and at81_sequencial is null ";
        } else {
        	$where = " and at81_sequencial is not null ";
        }
        
        if ( @$chave_at40_sequencial != "" ){
          $where .= " and at40_sequencial = {$chave_at40_sequencial} ";
        }
        
        if ( isset($pesquisa_chave) ) {
        	$where .= " and at40_sequencial = {$pesquisa_chave} ";
        }
        
        if ( @$chave_at40_descr != "" ) {
          $where .= " and at40_descr ilike '%{$chave_at40_descr}%'";
        }
        
        $sSqlTarefas = "  select at40_sequencial,
                                 at40_descr,
                                 login as  at40_responsavel,
                                 at40_diaini,
                                 at40_diafim,
                                 at25_descr as Area
											      from tarefa
											           inner join db_usuarios        on db_usuarios.id_usuario                = tarefa.at40_responsavel
											           inner join tarefamotivo       on tarefamotivo.at55_tarefa              = tarefa.at40_sequencial
										             inner join tarefasyscadproced on tarefasyscadproced.at37_tarefa        = tarefa.at40_sequencial
                                 inner join db_syscadproced    on tarefasyscadproced.at37_syscadproced  = db_syscadproced.codproced	
                                 inner join atendcadarea       on atendcadarea.at26_sequencial          = db_syscadproced.codarea
											           left  join tarefaprevisao     on tarefaprevisao.at81_tarefa            = tarefa.at40_sequencial  
						 				       where tarefa.at40_progresso < 100 
						 				         and tarefamotivo.at55_motivo in (2,3)
						 				             $where
											     order by at40_sequencial desc ";
      
      if(!isset($pesquisa_chave)){
      	
        if ( !isset($pesquisar) ) {
          $sSqlTarefas = "";
        }      	
      	
        db_lovrot($sSqlTarefas,30,"()","",$funcao_js);
        
      } else {
      	
        if ( $pesquisa_chave != null && $pesquisa_chave != "" ){
          
        	$rsTarefa = db_query($sSqlTarefas);
          
          if ( pg_num_rows($rsTarefa) > 0 ) {
            db_fieldsmemory($rsTarefa,0);
            echo "<script>".$funcao_js."('$at40_descr',false);</script>";
          } else {
	          echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        } else {
	        echo "<script>".$funcao_js."('',false);</script>";
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
