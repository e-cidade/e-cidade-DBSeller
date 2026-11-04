<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBseller Servicos de Informatica             
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

//MODULO: educação
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clnecessidadetipoatendimento = new cl_necessidadetipoatendimento;
$clnecessidadetipoatendimento->rotulo->label("ed186_descricao");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
<div class="container">
<table>
<form name="form2" method="post" action="" >
  <tr>
    <td>
      <fieldset>
      <legend>Tipos de Atendimento</legend>
      <table>
          <tr>
            <td width="4%" align="right" nowrap title="ed186_sequencial">
              <b>Sequencial:</b>
            </td>
            <td width="96%" align="left" nowrap>
              <?php db_input("ed186_sequencial",10,1,true,"text",4);?>
            </td>
          </tr>

          <tr>
            <td width="4%" align="right" nowrap title="Descrição">
              <b>Descrição:</b>
            </td>
            <td width="96%" align="left" nowrap>
              <?php db_input("ed186_descricao",40,3,true,"text",4);?>
            </td>
          </tr>
      </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <br/>
      <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
      <input name="limpar" type="reset" id="limpar" value="Limpar" onclick="js_limpar()">
      <input name="Fechar" type="button" id="fechar" value="Fechar"
        onClick="parent.db_iframe_necessidadesubgrupo.hide();">
    </td>
  </tr>
  </form>
  <tr>
    <td align="center" valign="top">
      <?php
      $aWhere = array();

      if(!isset($pesquisa_chave)){
        $campos = '*';

        if(isset($ed186_sequencial) && (trim($ed186_sequencial)!="") ){
          $aWhere[] = "necessidadetipoatendimento.ed186_sequencial = $ed186_sequencial";
        }

        if(isset($ed186_descricao) && (trim($ed186_descricao)!="") ){
          $aWhere[] = "ed186_descricao ilike '%$ed186_descricao%'";
        }

        $sWhere = implode(' and ', $aWhere);

        $sSql = $clnecessidadetipoatendimento->sql_query("", $campos, "ed186_sequencial", $sWhere);
        db_lovrot($sSql,15,"()","",$funcao_js);
      } else {
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $sWhere = "ed186_sequencial = ".$pesquisa_chave;
          $sSql = $clnecessidadetipoatendimento->sql_query(null, "*", null, $sWhere);
          $result = $clnecessidadetipoatendimento->sql_record($sSql);
          if($clnecessidadetipoatendimento->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."(false,'$ed186_descricao');</script>";
          } else {
            echo "<script>".$funcao_js."(true,'Chave(".$pesquisa_chave.") não Encontrado');</script>";
          }
        } else {
          echo "<script>".$funcao_js."(false,'');</script>";
        }
      }
      ?>
    </td>
  </tr>
</table>
</div>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
function js_limpar() {
  document.form2.ed186_sequencial.setAttribute('value', "");
  document.form2.ed186_descricao.setAttribute('value', "");
}
</script>
