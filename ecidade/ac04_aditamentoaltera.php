<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta" . ".php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("dbforms/db_funcoes.php");
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <?php db_app::load("estilos.css, prototype.js, scripts.js, strings.js, AjaxRequest.js"); ?>
  <?php db_app::load("datagrid.widget.js"); ?>
</head>
<body>

<div class="container">

  <fieldset>
    <legend class="bold">Manutenção de Aditamento</legend>
    <table>

      <tr>
        <td><label for="contrato_codigo"><?php db_ancora("<b>Acordo:</b>", "buscarContrato(true);", 1); ?></label></td>
        <td>
          <?php
          db_input('contrato_codigo', 10, 1, true, 'hidden', 3);
          db_input('contrato_numero', 60, 0, true, 'text', 3," onchange='buscarContrato(false);'");
          ?>
        </td>
      </tr>


      <tr>
        <td><label for="posicao"><b>Posição</b>:</label></td>
        <td>
          <?php
          db_input('posicao', 10, 1, true, 'text', 3);
          ?>
        </td>
      </tr>

      <tr>
        <td><label for="data_inicio"><b>Data Inicio</b>:</label></td>
        <td>
          <?php
          db_inputdata('data_inicio', '' , '', '', true, 'text', 1);
          ?>
        </td>
      </tr>


      <tr>
        <td><label for="data_fim"><b>Data Fim</b>:</label></td>
        <td>
          <?php
          db_inputdata('data_fim', '' , '', '', true, 'text', 1);
          ?>
        </td>
      </tr>

    </table>
  </fieldset>

  <input type="button" id="btnSalvar" onClick="salvar();" value="Salvar" disabled="" />

  <fieldset>
    <legend class="bold">Posições</legend>
    <div id="ctnGridposicaos"></div>

  </fieldset>
</div>

<?php
db_menu();
?>


<script>

  var oInputCodigoContrato = $('contrato_codigo');
  var oInputNumeroContrato = $('contrato_numero');
  var oInputDataInicio     = $('data_inicio');
  var oInputDataFim        = $('data_fim');
  var oInputPosicao        = $('posicao');

  var oGridPosicoes = new DBGrid('oGridPosicoes');
  oGridPosicoes.nameInstance = 'oGridPosicoes';
  oGridPosicoes.setHeader(['Posição', 'Data Inicio', 'Data Fim', 'Ação']);
  oGridPosicoes.setCellAlign(['center', 'center', 'center', 'center']);
  oGridPosicoes.setCellWidth(['20%', '20%', '20%', '30%']);
  oGridPosicoes.show($('ctnGridposicaos'));

  function salvar() {

    if (empty(oInputCodigoContrato.value) || empty(oInputNumeroContrato.value)) {
      return alert("Campo Código do Contrato é de preenchimento obrigatório.");
    }

    if (empty(oInputDataInicio.value)) {
      return alert("Campo Data Inicio é de preenchimento obrigatório.");
    }

    if (empty(oInputDataFim.value)) {
      return alert("Campo Data Fim é de preenchimento obrigatório.");
    }


    if (empty(oInputPosicao.value)) {
      return alert("Campo Posição é de preenchimento obrigatório.");
    }

    var oParametro = {
      exec : "salvar",
      codigo_contrato : oInputCodigoContrato.value,
      posicao         : oInputPosicao.value,
      datainicio      : $F('data_inicio'),
      datafim         : $F('data_fim')
    };

    new AjaxRequest(
      'con4_manutencaoaditamento.RPC.php',
      oParametro,
      function(oRetorno, lErro){

        alert(oRetorno.mensagem.urlDecode());
        limparCampos();
        getPosicoes();
      }
    ).setMessage("Aguarde, salvando informações...").execute();
  }


  function getPosicoes() {

    var oParametro = {
      exec : 'getPosicoesPorContrato',
      codigo_contrato : oInputCodigoContrato.value
    };

    new AjaxRequest(
      'con4_manutencaoaditamento.RPC.php',
      oParametro,
      function(oRetorno, lErro){

        if (lErro) {
          return alert(oRetorno.mensagem.urlDecode());
        }

        oGridPosicoes.clearAll(true);
        oRetorno.posicoes.each(
          function (oPosicao, iIndice) {

            var sBotao  = "<input type='button' style='margin: 2px;' onclick='alterar("+iIndice+");' value='Alterar'/>";
                sBotao += "<input type='button' style='margin: 2px;' onclick='excluir("+oPosicao.posicao+");' value='Excluir'/>";

            var aLinha = [
              oPosicao.posicao,
              oPosicao.datainicio,
              oPosicao.datafim,
              sBotao
            ];
            oGridPosicoes.addRow(aLinha);
          }
        );
        oGridPosicoes.renderRows();
      }
    ).setMessage("Aguarde, carregando posicaos..").execute();
  }

  function excluir(iCodigoposicao) {

    let mensagemExclusao = 'Confirma a exclusão? \n'
    mensagemExclusao += 'Esta ação excluirá também o evento criado de '
    mensagemExclusao += 'forma automática a partir da inclusão da posição, '
    mensagemExclusao += 'bem como documentos anexados ao mesmo.'

    if (!confirm(mensagemExclusao)) {
      return false;
    }

    new AjaxRequest(
      'con4_manutencaoaditamento.RPC.php',
      {exec : 'excluir', posicao : iCodigoposicao},
      function (oRetorno, lErro) {
        alert(oRetorno.mensagem.urlDecode());
        getPosicoes();
        limparCampos();
      }
    ).setMessage('Aguarde, excluindo posicao...').execute();
  }

  function alterar(iCodigoLinha){

    var posicao    = oGridPosicoes.aRows[iCodigoLinha].aCells[0].getValue();
    var datainicio = oGridPosicoes.aRows[iCodigoLinha].aCells[1].getValue();
    var datafim    = oGridPosicoes.aRows[iCodigoLinha].aCells[2].getValue();

    oInputDataInicio.value = datainicio;
    oInputDataFim.value    = datafim;
    oInputPosicao.value    = posicao;

    $('btnSalvar').disabled = false;

  }

  function limparCampos() {

    oInputDataInicio.value = '';
    oInputDataFim.value    = '';
    oInputPosicao.value    = '';

    $('btnSalvar').disabled = true;

  }

  function buscarContrato(lMostra) {

    js_OpenJanelaIframe(
      'top.corpo',
      'db_iframe_bidcontrato',
      'func_acordo.php?funcao_js=parent.preencheNumeroContrato|ac16_sequencial|ac16_resumoobjeto',
      'Pesquisa Contrato',true
    );
  }

  function preencheNumeroContrato(iCodigo, iNumero) {

    oInputCodigoContrato.value = iCodigo;
    oInputNumeroContrato.value = iNumero;
    db_iframe_bidcontrato.hide();
    getPosicoes();
    limparCampos()
  }
</script>

</body>
</html>
