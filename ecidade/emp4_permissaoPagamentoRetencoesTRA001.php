<?php
/**
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
require_once(modification("dbforms/db_funcoes.php"));

$oPost = db_utils::postMemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

if (isset($oPost->salvar)) {

    try {
      $cl_retencaotiporecorcunidadeliberaremessa = new cl_retencaotiporecorcunidadeliberaremessa();
      $cl_retencaotiporecorcunidadeliberaremessa->excluir(null, null, null, "e287_instituicao = ".db_getsession("DB_instit"));
      if ($cl_retencaotiporecorcunidadeliberaremessa->erro_status == "0") {
          throw new Exception($cl_retencaotiporecorcunidadeliberaremessa->erro_msg);
      }
      
      if (!empty($oPost->unidades)) {
        $aUnidades = explode(",",$oPost->unidades);
        
        foreach($aUnidades as $sUnidade) {
            list($orgao,$unidade,$instituicao) = explode("|",$sUnidade);
            
            $cl_retencaotiporecorcunidadeliberaremessa->incluir($orgao, $unidade, $instituicao);
            if ($cl_retencaotiporecorcunidadeliberaremessa->erro_status == "0") {
                throw new Exception($cl_retencaotiporecorcunidadeliberaremessa->erro_msg);
            }
        }
      }
      db_fim_transacao(false);
      
      db_msgbox("Operação realizada com sucesso");
      
    } catch (Exception $e) {
        db_fim_transacao(true);
        db_msgbox($e->getMessage());
    }
    
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body>
<form name="form" id="form" method="post">
 <div class="container">
   <fieldset>
     <legend>Libera U.O para pagamento de Retenções por RE</legend>
     <table class="form-container">
       <tr>
         <td> 
           <input type='checkbox' name='check_all' id='check_all' onclick="checkAll()"> Marcar/Desmarcar Todos
         </td>
       </tr>
       <?php
         $sHashOrgao = "";
         $sSqlOrgaoUnidade = "select orcunidade.o41_orgao,
                                     orcunidade.o41_unidade,
                                     orcunidade.o41_instit,
                                     orcunidade.o41_descr,
                                     orcorgao.o40_descr,
                                     retencaotiporecorcunidadeliberaremessa.e287_unidade 
                                from orcunidade
                                     inner join orcorgao on o40_orgao = o41_orgao
                                                        and o40_anousu = o41_anousu 
                                      left join retencaotiporecorcunidadeliberaremessa on e287_orgao = o41_orgao
                                                                                      and e287_unidade = o41_unidade
                                                                                      and e287_instituicao = o41_instit
                               where o41_anousu = ".db_getsession("DB_anousu")."
                                 and o41_instit = ".db_getsession("DB_instit")."
                               order by o41_orgao, o41_unidade";
         $rsOrgaoUnidade = db_query($sSqlOrgaoUnidade);
         $iQtdLinhasOrgaoUnidade = pg_num_rows($rsOrgaoUnidade);
         for ($iInd = 0; $iInd < $iQtdLinhasOrgaoUnidade; $iInd++) {
             
           $oDados = db_utils::fieldsMemory($rsOrgaoUnidade, $iInd);
             
           $sNomeCampo = "OU_".$oDados->o41_orgao."_".$oDados->o41_unidade."_".$oDados->o41_instit;
           
           if ($sHashOrgao != $oDados->o41_orgao) {
               echo "<tr>";
               echo " <td class='table_header'>";
               echo " <input type='checkbox' name='check_ou{$oDados->o41_orgao}' id='check_ou{$oDados->o41_orgao}' onclick='checkOU({$oDados->o41_orgao})'>";
               echo " {$oDados->o41_orgao} - {$oDados->o41_descr}"; 
               echo " </td>";
               echo "</tr>";
           }
           $sHashOrgao = $oDados->o41_orgao;
           
           $sChecked = "";
           if ($oDados->e287_unidade != "") {
               $sChecked = " checked ";
           }
           echo "<tr>";
           echo " <td> ";
           echo "   <input type='checkbox' name='{$sNomeCampo}' id='{$sNomeCampo}' {$sChecked}> ";
           echo $oDados->o41_orgao.".".str_pad($oDados->o41_unidade,3,"0",STR_PAD_LEFT)." - ".$oDados->o41_descr;
           echo " </td>";
           echo "</tr>";
           
         }
       ?>  
     </table>
   </fieldset> 
  <input name="salvar" id="salvar" type="button" value="Salvar" onclick="enviarDados();" > 
</div> 
</form>   
</body>
</html>

<script>
function checkAll() {
  aInputs = document.getElementsByTagName("INPUT");
  for (iInd = 0; iInd < aInputs.length; iInd++) {
    var Item = aInputs[iInd];
    if (Item.type == "checkbox") {
        if ($('check_all').checked) {
    	  Item.checked = true;
        } else {
          Item.checked = false;
        }
    }
  }
}

function checkOU(Orgao) {
	
  aInputs = document.getElementsByTagName("INPUT");
  for (iInd = 0; iInd < aInputs.length; iInd++) {
	var Item = aInputs[iInd];
	aItem = Item.name.split("_");
	if (Item.type == "checkbox" && (aItem[0] == "OU" && aItem[1] == Orgao)) {
        if ($('check_ou'+Orgao).checked) {
         Item.checked = true;
        } else {
          Item.checked = false;
        }
    }
  }
}

function enviarDados() {
	
  var Unidades = [];
  
  aInputs = document.getElementsByTagName("INPUT");
  for (iInd = 0; iInd < aInputs.length; iInd++) {
  	var Item = aInputs[iInd];
  	aItem = Item.name.split("_");
  	if (Item.type == "checkbox" && aItem[0] == "OU") {
        if (Item.checked) {
          Unidades.push(aItem[1]+"|"+aItem[2]+"|"+aItem[3]);  
        }
    }
  }

  inputUnidades = document.createElement("input");
  inputUnidades.setAttribute("type", "text");
  inputUnidades.setAttribute("name", "unidades");
  inputUnidades.setAttribute("value", Unidades);
  $("form").append(inputUnidades);

  inputUnidades = document.createElement("input");
  inputUnidades.setAttribute("type", "text");
  inputUnidades.setAttribute("name", "salvar");;
  $("form").append(inputUnidades);  
  
  $("form").submit();
  
}

</script>