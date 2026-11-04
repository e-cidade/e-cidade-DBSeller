<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_liclicitem_classe.php"));
require_once(modification("classes/db_liclicita_classe.php"));
require_once(modification("classes/db_liclicitaitemlog_classe.php"));
require_once(modification("model/licitacao.model.php"));
require_once(modification("model/licitacao/SituacaoLicitacao.model.php"));

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
  <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body style="margin-top: 5px; background-color: #CCCCCC;">
<center>
  <fieldset>
    <legend><b>Itens da Licitação</b></legend>
    <div id="ctnGridItens"></div>
  </fieldset>
</center>
</body>
</html>

<script>

  let aHeaders = [
      "Ordem",
      "Qtd. Solicitada",
      "Vlr. Unitário",
      "Vlr. Total",
      "Unidade",
      "Material",
      "Lote",
      "ME/EPP",
      "Fornecedor",
      "Observa&ccedil;&otilde;es"
  ];

  let aCellAlign = [
      "center",
      "center",
      "right",
      "right",
      "left",
      "left",
      "left",
      "left",
      "left",
      "left"
  ];

  let aCellWidth = [
      "5%",
      "8%",
      "8%",
      "10%",
      "11%",
      "21%",
      "11%",
      "5%",
      "10%",
      "8%"
  ];

  var oGridItens = new DBGrid("oGridItens");
  oGridItens.sNameInstance = "oGridItens";
  oGridItens.setHeader(aHeaders);
  oGridItens.setCellAlign(aCellAlign);
  oGridItens.setCellWidth(aCellWidth);
  oGridItens.show($('ctnGridItens'));

  oGridItens.setStatus("Coloque o cursor sob a linha para obter mais informações.");

  var oGet = js_urlToObject();
  var oParametro = {"exec" : "getItensConsultaLicitacao", "iCodigoLicitacao" : oGet.l20_codigo};

  js_divCarregando("Aguarde, carregando itens da licitação", "msgBox");
  new Ajax.Request("lic4_licitacao.RPC.php",
                   {
                        method: 'post',
                        asynchronous: false,
                        parameters: 'json=' + Object.toJSON(oParametro),
                        onComplete: completarGrid
                   });

  function completarGrid(oAjax) {
      js_removeObj("msgBox");
      const oRetorno = JSON.parse(oAjax.responseText);
      oGridItens.clearAll(true);
      if (oRetorno.aItens.length === 0) {
          return alert(`Nenhum item encontrado para a licitação ${oGet.l20_codigo}.`);
      }

      oRetorno.aItens.each(function (oItem, iIndice) {
          let aLinha = [
              oItem.iOrdem,
              oItem.iQuantidade,
              oItem.nValorUnitario,
              oItem.nValorTotal,
              oItem.sUnidadeDeMedida.urlDecode(),
              oItem.sDescricaoMaterial.urlDecode().substring(0, 35),
              oItem.sLote.urlDecode(),
              oItem.reservado ? 'Sim' : 'Não',
              oItem.sFornecedor.urlDecode().substring(0, 35),
              oItem.sObservacao.urlDecode().substring(0, 35)
          ];
          oGridItens.addRow(aLinha);
      });
      oGridItens.renderRows();

      /**
       * Adicionamos o texto completo quando o usuário passar o mouse por cima da linha
       */
      oRetorno.aItens.each(function (oItem, iIndice) {
          if (oItem.sFornecedor.trim() !== '') {
              let descricaoFornecedor = oItem.sFornecedor.urlDecode();
              descricaoFornecedor += oItem.tipoEmpresa ? "<br/>Tipo de Empresa: " + oItem.tipoEmpresa.urlDecode() : '';
              descricaoFornecedor += oItem.cgccpf ? "<br/>Documento: " + oItem.cgccpf.urlDecode() : '';

              oGridItens.setHint(iIndice, 8, descricaoFornecedor);
          }

          if (oItem.sLote.trim() !== '') {
              oGridItens.setHint(iIndice, 6, oItem.sLote.urlDecode());
          }

          if (oItem.sResumo.trim() !== '') {
              oGridItens.setHint(iIndice, 5, oItem.sResumo.urlDecode());
          } else if (oItem.sDescricaoMaterial.trim() !== '') {
              oGridItens.setHint(iIndice, 5, oItem.sDescricaoMaterial.urlDecode());
          }

          if (oItem.sObservacao.trim() !== '') {
              oGridItens.setHint(iIndice, 9, oItem.sObservacao.urlDecode());
          }

      });
  }
</script>
