<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>    
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div class="container">
      <fieldset>
        <legend>Suspensão Orçamentária</legend>
        <div id='ctnGridSuspensao'></div>
      </fieldset>
      <input type='button' name='btnSalvarSuspensao' value='Salvar' onclick="atualizarSuspensao()"/>
    </div>
  </body>
</html>

<script>
  const urlRPC = "orc4_suspensaoorcamentaria.RPC.php";

  
  function criaGridSuspensao() 
  {
    oGridSuspensao = new DBGrid('oGridSuspensao');
    oGridSuspensao.nameInstance = 'oGridSuspensao';

    oGridSuspensao.setCellWidth(new Array('50px' ,
                                          '400px',
                                          '50px',
                                          '60px',
                                          '60px',
                                          '60px',
                                          '70px',
                                          '70px',
                                          '60px',
                                    )
    );

    oGridSuspensao.setCellAlign(new Array('center'  ,
                                          'left',
                                          'center',
                                          'center',
                                          'center',
                                          'center',
                                          'center',
                                          'center',
                                          'center',
                                    )
    );

    oGridSuspensao.setHeader(new Array('Órgão',
                                       'Unidade',
                                       'Anexo',
                                       'Recurso',
                                       'Fonte',
                                       'Empenho',
                                       'Liquidação',
                                       'Pagamento',
                                       'Cod_Unidade'
                                 )  
    );

    oGridSuspensao.setHeight(400);
    oGridSuspensao.aHeaders[8].lDisplayed = false;
    oGridSuspensao.show($('ctnGridSuspensao'));

    carregaDadosSuspensao();
}  

criaGridSuspensao();

function carregaDadosSuspensao() 
{
  js_divCarregando('Carregando dados da suspensão...', 'msgBox');

  var oParam = new Object();
  oParam.sExec = "getDadosOrcamento";  
  
  var oAjax = new Ajax.Request(urlRPC, 
                            { method         : 'post',
                              parameters     : 'json=' + js_objectToJson(oParam),
                              onComplete     : preencheGridSuspensao
                            });
}

function preencheGridSuspensao(oAjax)
{
  js_removeObj("msgBox");

  var oRetorno = eval("("+oAjax.responseText+")");

  if (oRetorno.iStatus == "2") {
    alert(oRetorno.sMessage.urlDecode());
  }  
  else {

    oGridSuspensao.clearAll(true);

    for (var i = 0; i < oRetorno.suspensoes.length; i++) {

      var aCelulas = Array();
      with (oRetorno.suspensoes[i]) {
        var id = o58_orgao+'_'+o58_unidade+'_'+o58_localizadorgastos+'_'+o58_codigo;

        aCelulas[0] = o58_orgao;
        aCelulas[1] = o58_unidade.padStart(2,'0')+" - "+o41_descr.urlDecode();
        aCelulas[2] = o58_localizadorgastos;
        aCelulas[3] = o58_codigo;
        aCelulas[4] = gestao;
        
        aCelulas[5] = "<input type='checkbox' id='suspEmpenho_"+id+"' ";
        aCelulas[5] += "name='suspEmpenho' "+((suspenderempenho=='t')?"checked":"")+" />";
        
        aCelulas[6] = "<input type='checkbox' id='suspLiquidacao_"+id+"' ";
        aCelulas[6] += "name='suspLiquidacao' "+((suspenderliquidacao=='t')?"checked":"")+" type='checkbox' />";
        
        aCelulas[7] = "<input type='checkbox' id='suspPagamento_"+id+"' ";
        aCelulas[7] += "name='suspPagamento' "+((suspenderpagamento=='t')?"checked":"")+" type='checkbox' />";

        aCelulas[8] = o58_unidade;
      }
      oGridSuspensao.addRow(aCelulas);
    }
    oGridSuspensao.renderRows();
  }
}

function atualizarSuspensao() 
{
  js_divCarregando('Realizando alterações na suspensão...', 'msgBox');

  var suspensoes = Array();

  for (var i = 0; i < oGridSuspensao.getNumRows(); i++) {
    
    var orgao = oGridSuspensao.aRows[i].aCells[0].getValue();
    var unidade = oGridSuspensao.aRows[i].aCells[8].getValue();
    var anexo = oGridSuspensao.aRows[i].aCells[2].getValue();
    var recurso = oGridSuspensao.aRows[i].aCells[3].getValue();

    var id = orgao+'_'+unidade+'_'+anexo+'_'+recurso;

    suspensao = new Object();
    suspensao.orgao = orgao;
    suspensao.unidade = unidade;
    suspensao.anexo = anexo;
    suspensao.recurso = recurso;
    suspensao.suspensaoEmpenho = document.getElementById('suspEmpenho_'+id).checked;
    suspensao.suspensaoLiquidacao = document.getElementById('suspLiquidacao_'+id).checked;
    suspensao.suspensaoPagamento = document.getElementById('suspPagamento_'+id).checked;
    suspensoes.push(suspensao);
  }

  var oParam = new Object();
  oParam.sExec = "atualizarSuspensao";  
  oParam.suspensoes = suspensoes;

  var oAjax = new Ajax.Request(urlRPC, 
                            { method         : 'post',
                              parameters     : 'json=' + js_objectToJson(oParam),
                              onComplete     : retornoAtualizacao
                            });
}

function retornoAtualizacao(oAjax)
{
  js_removeObj("msgBox");

  var oRetorno = eval("("+oAjax.responseText+")");
  alert(oRetorno.sMessage.urlDecode());
  
  criaGridSuspensao();

}

</script>
