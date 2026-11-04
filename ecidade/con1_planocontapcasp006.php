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
 
  require_once(modification("libs/db_stdlib.php"));
  require_once(modification("libs/db_utils.php"));
  require_once(modification("libs/db_app.utils.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_libdicionario.php"));
  require_once(modification("libs/db_libcontabilidade.php"));
  require_once(modification("dbforms/db_funcoes.php"));
  require_once(modification("libs/db_sessoes.php"));
  require_once(modification("libs/db_usuariosonline.php"));
  require_once(modification("dbforms/db_classesgenericas.php"));
  require_once(modification("classes/db_conparametro_classe.php"));
  
  $oGet = db_utils::postMemory($_GET);
?>


<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
  db_app::load("scripts.js");
  db_app::load("prototype.js"); 
  db_app::load("strings.js, grid.style.css, datagrid.widget.js, AjaxRequest.js" );
?>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin-top:25px;">
<center>
  <form>
    <fieldset style="width: 500px;">
      <legend><b>Conta Corrente</b></legend>
      <table>

      <tr id='conta-corrente'>
          <td>
              <?php
              db_ancora("<b>Conta Corrente:</b>", "js_pesquisaContaCorrente(true)", 1);
              ?>
          </td>
          <td nowrap="nowrap">
              <?php
              db_input("iCodigoContaCorrente", 10, null, true, "text", 1, "onchange='js_pesquisaContaCorrente(false);'");
              db_input("sDescricaoContaCorrente", 35, null, true, "text", 3);
              ?>
          </td>
      </tr>

      </table>
    </fieldset>
    <p>
      <input type="button" name="btnIncluirContaOrcamento" id="btnIncluirContaOrcamento" value="Incluir" />
    </p>
  </form>
  <fieldset style="width: 800px;">
    <legend><b>Contas Correntes Vinculadas</b></legend>
      <div id="divContasCorrentesVinculadas">
      </div>
  </fieldset>
</center>
</body>
</html>

<script>

/*
 * Codigo da Conta Passada por GET
 */
var iCodigoConta = <?=@$oGet->iCodigoConta?>;


var oGridContasCorrentes              = new DBGrid('oGridContasCorrentes');
    oGridContasCorrentes.nameInstance = 'oGridContasCorrentes';
    oGridContasCorrentes.sName        = 'oGridContasCorrentes';
    oGridContasCorrentes.setCellAlign = (["center","left", "right"]);
    aHeaders                          = ["Código", "Descrição Conta" , "Ação"];
    oGridContasCorrentes.aWidths      = [10, 30, 10];
    oGridContasCorrentes.setHeader(aHeaders);
    oGridContasCorrentes.show($('divContasCorrentesVinculadas'));


function js_carregarContasCorrentes() {

  var oParam          = new Object();
  oParam.exec         = "getContaCorrente";
  oParam.iCodigoConta = iCodigoConta;

  oGridContasCorrentes.clearAll(true);
  new AjaxRequest( "con4_conplanoPCASP.RPC.php", oParam, function (resposta, erro ){

      resposta.contasCorrentes.each(function ( contacorrente ) {
          var sBotaoExcluir = "<input value='Excluir' type='button' onclick='js_excluirContaCorrenteVinculado("+contacorrente.codigo+")' />";
          oGridContasCorrentes.addRow([contacorrente.codigo, contacorrente.descricao.urlDecode(), sBotaoExcluir]);
      });
      oGridContasCorrentes.renderRows();
  }).execute();

}

function js_excluirContaCorrenteVinculado( codigo ) {

    if (!confirm('Confirma a exclusão do conta corrente vinculado?')) {
        return false;
    }

    new AjaxRequest(
        'con4_conplanoPCASP.RPC.php',
        {'exec': 'excluirContaCorrente', 'iCodigoPlanoPCASP' : iCodigoConta, 'codigoContaCorrente' : codigo },
        function (retorno, erro) {

            alert(retorno.message.urlDecode());
            if (!erro) {
                js_carregarContasCorrentes();
            }
        }
    ).execute();

}


$("btnIncluirContaOrcamento").observe("click", function() {


  js_divCarregando("Aguarde, incluindo conta corrente para a conta selecionada...", "msgBox");
  var oParam                  = new Object();
  oParam.exec                 = "vinculaContaCorrente";
  oParam.iCodigoPlanoPCASP    = iCodigoConta;
  oParam.iCodigoContaCorrente = $("iCodigoContaCorrente").value;
  
  var oAjax = new Ajax.Request("con4_conplanoPCASP.RPC.php",
                                {
                                 method:'post',
                                 parameters:'json='+Object.toJSON(oParam),
                                 onComplete: js_retornoVinculaContaCorrente
                                }
                               );
});

/**
 * Retorno da ação de SALVAR o vinculo entre plano de contas
 */
function js_retornoVinculaContaCorrente(oAjax) {

  js_removeObj("msgBox");
  var oRetorno = JSON.parse(oAjax.responseText);
  alert(oRetorno.message.urlDecode());
    js_carregarContasCorrentes();
  $("iCodigoContaCorrente").value = "";
  $("sDescricaoContaCorrente").value = "";
}

function js_pesquisaContaCorrente(lMostraWindow) {

    if (lMostraWindow) {
        var sUrl = 'func_conplanosistema.php?tipo=2&funcao_js=parent.js_preencheContaCorrente|c122_sequencial|c122_descricao';
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_contacorrente','db_iframe_conplanosistema',sUrl,'Pesquisa de Conta Corrente',true,'0');
    } else {

        if ($("iCodigoContaCorrente").value != '') {
            var sUrl  = 'func_conplanosistema.php?tipo=2&pesquisa_chave='+$F("iCodigoContaCorrente");
            sUrl +='&funcao_js=parent.js_completaContaCorrente';
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_contacorrente','db_iframe_conplanosistema',sUrl,'Pesquisa',false);
        } else {
            $("sDescricaoRecurso").value = '';
        }
    }
}

function js_completaContaCorrente(sDescricaoContaCorrente, erro) {

    $('sDescricaoContaCorrente').value = sDescricaoContaCorrente;
    if (erro) {
        $('iCodigoContaCorrente').value = '';
    }
}

function js_preencheContaCorrente(iCodigoContaCorrente, sDescricaoContaCorrente) {

    $('iCodigoContaCorrente').value    = iCodigoContaCorrente;
    $('sDescricaoContaCorrente').value = sDescricaoContaCorrente;
    db_iframe_conplanosistema.hide();
}

js_carregarContasCorrentes();

</script>
