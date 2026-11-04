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
include(modification("classes/db_cancdebitosreg_classe.php"));
include(modification("classes/db_cancdebitosprot_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clcancdebitosreg = new cl_cancdebitosreg;
$clcancdebitosprot = new cl_cancdebitosprot;    

        if($pesquisa_chave2!=null && $pesquisa_chave2!=""){
        	$k25_codproc = "";
        	$p58_requer = "";
          //$result = $clcancdebitosreg->sql_record($clcancdebitosreg->sql_query("","k20_descr,k21_obs",""," k21_codigo= ".$pesquisa_chave2));
					
					$sql = "select distinct k20_descr,k21_obs,k72_concarpeculiar,c58_descr,k20_cancdebitostipo
					          from cancdebitosreg 
										inner join cancdebitos on cancdebitos.k20_codigo = cancdebitosreg.k21_codigo 
										       and cancdebitos.k20_instit = ".db_getsession("DB_instit")." 
									  left  join cancdebitosconcarpeculiar on k72_cancdebitos = k20_codigo 
										left  join concarpeculiar on k72_concarpeculiar = c58_sequencial 
										where k21_codigo = {$pesquisa_chave2} ";

					$result = db_query($sql);
				  $linhas = pg_num_rows($result);
				
          if($linhas!=0){
            db_fieldsmemory($result,0);
            $result_prot=$clcancdebitosprot->sql_record($clcancdebitosprot->sql_query($pesquisa_chave2));
						
            if ($clcancdebitosprot->numrows>0){
            	db_fieldsmemory($result_prot,0);
            }
            echo "<script>".$funcao_js."('$k20_descr','$k21_obs','$k25_codproc','$p58_requer','$k72_concarpeculiar','$c58_descr','$k20_cancdebitostipo' );</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave2.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      //}
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
