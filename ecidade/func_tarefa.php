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
include(modification("classes/db_db_proced_classe.php"));
include(modification("classes/db_tarefa_classe.php"));
include(modification("classes/db_tarefa_aut_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cldb_proced  = new cl_db_proced;
$cltarefa     = new cl_tarefa;
$cltarefa_aut = new cl_tarefa_aut;
$cltarefa->rotulo->label("at41_proced");
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
	     <?php
	   
	     	if (@$aut==1) { // 1
	     	  
	     	  $rsDensenvolvedorErro = db_query("select * from db_desenvolvedorerro where usuario = " . db_getsession("DB_id_usuario"));
	     	  $iLinhasDesenvolvedorErro = pg_num_rows($rsDensenvolvedorErro);
	     	  if ($iLinhasDesenvolvedorErro == 0) {

	     	    $resultado = $cldb_proced->sql_record($cldb_proced->sql_query_aut(null," distinct at30_codigo,at30_descr","at30_codigo", "at56_usuario = " . db_getsession("DB_id_usuario"))); 	
            $linhas  = $cldb_proced->numrows;
            if ($linhas  ==  0) {
              db_msgbox("Voce não tem permissão para autorizar nenhum procedimento!");
				      exit;
            } 
            
	     	    ?>
            <tr> 
              <td width="4%" align="right" nowrap title="<?=@$Tat41_proced?>"><b>Procedimento:</b></td>
              <td width="96%" align="left" nowrap>
             		<select name="chave_at41_proced" >
              	 <option value="0">TODOS</option>
	              <?php
						     for ($i = 0; $i < $linhas; $i++) {//3
							     db_fieldsmemory($resultado,$i);	
							     $selected = "";	
							     if ($chave_at41_proced > 0) {//4
								     if ($chave_at41_proced == $at30_codigo) { //5
									        $selected = "SELECTED";
									   } else { //6
									        $selected = "";
								     } // f 6
							    } // f 4
					      ?>
						      <option value="<?=$at30_codigo ?>" <?=$selected?>><?=$at30_descr?></option>
						    <?php
						      }// f 3
						    ?>
				        </select>
              </td>
            </tr>
            <tr>
          	  <td width="4%" align="right" ><b>Situação:</b> </td>
          	  <td width="96%" align="left" >
			        <?php 	
					      $usu = db_getsession("DB_id_usuario");
					      $sqlsutusu = " select distinct * 
					                       from ( select at46_codigo,
									                             at46_descr 
								                          from tarefacadsituacaousu 
								                         inner join tarefacadsituacao on at17_tarefacadsituacao = at46_codigo 
								                         where at17_usuario = $usu
								                         union all
								                        select at46_codigo,
									                             at46_descr
								                          from tarefacadsituacao 
								                         where at46_codigo = 2 ) as x
								                order by at46_codigo ";
         				$resultsutusu = db_query($sqlsutusu);
					      $linhassutusu = pg_num_rows($resultsutusu);
					      if ($linhassutusu>0) {
						    ?>
						     <select name="situacao" >
	              		<option value="0">TODOS</option>
	              	 <?php for ($x = 0; $x < $linhassutusu; $x++) {
							          db_fieldsmemory($resultsutusu,$x);
							          if ($situacao == $at46_codigo){
								          $selected1 = "SELECTED";
							          } else {
								          $selected1 ="";
							          }	
						       ?>
						        <option value="<?=$at46_codigo ?>" <?=$selected1?>><?=$at46_descr?></option>
						       <?php
						          }
						       ?>
						     </select>
						    <?php
					      }
         				?>
				      </td>
			      </tr>
				<?php
		      }  
	     	}
	     ?>
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
           <?php db_input("chave_at40_descr",40,"",true,"text",4); ?>
         </td>
       </tr>
          <?php 
          if (@$prorrogar != 1) {
            
            if((@$aut!=1)&&(@$aut!='t')) { 
              echo " <tr>
				              <td><b>Autorização:</b> </td>
				              <td>
          				     <select name=\"autorizada\" >
          				   		 <option value=\"0\" >Todos</option>
          	             <option value=\"t\" >Autorizadas</option>
          	             <option value=\"f\" >Não Autorizadas</option>
          						 </select>
        				      </td>
			     					 </tr> ";
				    } else if($aut=="t") {
					    $autorizada='t';
				    } else {
					    $autorizada= 'f';
				    }
			    }
       		?>
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
      if (!isset($pesquisa_chave)) { 
      	     	
				if (@$prorrogar == 1) {
					$where = " at40_autorizada is true and at40_ativo is true"; 
					$where = " at40_ativo is true"; 
				} else {
					if ((@$autorizada=="0")||(@$autorizada=="")){
					    $where = " at40_ativo is true"; 
					}else{
						$where = " at40_autorizada= '$autorizada' and at40_ativo is true";    
					}
				}
      	
      	if (@$chave_at40_sequencial !=""){
      		$where .= " and at40_sequencial = $chave_at40_sequencial ";
      	}else{
      		if (@$chave_at41_proced!=0){
	      		$where .= " and at30_codigo = $chave_at41_proced";
	      	}
	      	if (@$situacao!=0){
	      		$where .= " and at47_situacao = $situacao";
	      	}
      	}

				if (@$chave_at40_descr !=""){
      		$where .= " and at40_descr ilike '%$chave_at40_descr%' or at40_obs ilike '%$chave_at40_descr%'";
				}
      	
        if ($iLinhasDesenvolvedorErro > 0) {
          if ($where != "") {
            $where .= " and "; 
          } 
          $where .= " at40_tipo in (1,6,13,16)";
        }
      				
        $sql  = " select distinct 
                         tarefa.at40_sequencial,
                         db_usuarios.login,
                         tarefa.at40_descr,
                         at40_autorizada,
				                 case tarefa.at40_prioridade 
				                   when 1 then 'Baixa' 
				                   when 2 then 'Média' 
				                   when 3 then 'Alta' 
				                 end as at40_prioridade, 
				                 db_usuarios2.login as dl_quem_cadastrou,
				                 at36_data,
				                 at36_hora,
				                 at36_ip 
			              from tarefa 
			                   inner join db_usuarios                 on db_usuarios.id_usuario     = tarefa.at40_responsavel 
			                    left join tarefa_lanc                 on tarefa.at40_sequencial     = tarefa_lanc.at36_tarefa 
			                                                         and at36_tipo                  = 'I' 
			                    left join db_usuarios as db_usuarios2 on tarefa_lanc.at36_usuario   = db_usuarios2.id_usuario 
			                    left join tarefaproced                on tarefaproced.at41_tarefa   = tarefa.at40_sequencial 
			                    left join db_proced                   on db_proced.at30_codigo      = tarefaproced.at41_proced 
			                   inner join tarefasituacao              on tarefasituacao.at47_tarefa = tarefa.at40_sequencial
			             where $where
			             order by at40_sequencial desc ";
      	
        if (!isset($pesquisar)) {
          $sql = "";
        }

        db_lovrot($sql,30,"()","",$funcao_js);
        
      } else {
      	
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $cltarefa->sql_record($cltarefa->sql_query($pesquisa_chave));
          if($cltarefa->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$at40_sequencial',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </td>
   </tr>
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
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
