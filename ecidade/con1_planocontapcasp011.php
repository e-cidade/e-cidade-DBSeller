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
$clrotulo = new rotulocampo;
$clrotulo->label("c52_descr");
$clrotulo->label("c61_reduz");
$clrotulo->label("c51_descr");
$clrotulo->label("c60_descr");
$clrotulo->label("codigo");
$clrotulo->label("c61_codigo");
$clrotulo->label("o15_descr");
$clrotulo->label("nomeinst");
$clrotulo->label("c90_estrutsistema");
$clrotulo->label("c60_estrut");
$clrotulo->label("c60_naturezasaldo");
$clrotulo->label("c64_descr");

$clrotulo->label("db89_db_bancos");
$clrotulo->label("db89_codagencia");
$clrotulo->label("db89_digito");
$clrotulo->label("db83_conta");
$clrotulo->label("db83_dvconta");
$clrotulo->label("db83_identificador");
$clrotulo->label("db83_codigooperacao");
$clrotulo->label("db83_tipoconta");
$GsTitulo        = 't';
$NsFuncionamento = 'style="background-color:#E6E4F1;"';
$NsFuncao        = 'style="background-color:#E6E4F1;"';

$oEstruturaSistema = new cl_estrutura_sistema();
$iOpcao = 1;
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js");
    db_app::load("prototype.js");
    db_app::load("strings.js");
    db_app::load("dbautocomplete.widget.js");
    db_app::load("DBViewContaBancaria.js");
    db_app::load("dbmessageBoard.widget.js");
    db_app::load("estilos.css");
    db_app::load("dbtextField.widget.js");
    db_app::load("dbcomboBox.widget.js");
    db_app::load("prototype.maskedinput.js");
    db_app::load("windowAux.widget.js");
    db_app::load("AjaxRequest.js");
    ?>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        select {width: 98%;}
        textarea {width: 100%;}
        input#c90_estrutcontabil:disabled{background-color: #DEB887;
            color:black}
    </style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">


<form name="form1" id='form1'>
    <center>
        <br />
        <fieldset style="width: 500px;">
            <legend><b>Plano de Contas PCASP</b></legend>
            <table border="0" width="500px;">
                <tr>
                    <td><b>Código:</b></td>
                    <td>
                        <?php
                        db_input("iCodigoConta", 5, false, 3, "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap="nowrap">
                        <b>Estrutural Contabilidade</b>
                    </td>
                    <td>
                        <?php
                        $mascara = '0.0.0.0.0.00.00.00.00.00';
                        db_input('mascara', 30, $Ic60_estrut, true, 'text', 3, "", "","", "width:98%;");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap="nowrap">
                        <b>Estrutural Contabilidade</b>
                    </td>
                    <td>
                        <?php
                        db_input('c90_estrutcontabil', 30, $Ic60_estrut, true, 'text', $db_opcao, "", "","", "width:98%;");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b>Título:</b></td>
                    <td>
                        <?php
                        db_input("sTitulo", 50, "0", true, "text", $db_opcao,"", "", "", "", 50);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b>Natureza de Saldo:</b></td>
                    <td>
                        <?php
                        $aNaturezaSaldo = array(1 => "Saldo Devedor",
                            2 => "Saldo Credor",
                            3 => "Ambos");
                        db_select("iNaturezaSaldo", $aNaturezaSaldo, true, $db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b>Transfere Saldo:</b></td>
                    <td>
                        <?php
                        $aTipoSaldo = array(
                            'f' => "Não",
                            't' => "Sim",
                            );
                        db_select("bSaldoContinuo", $aTipoSaldo, true, $db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b>Tipo de Conta:</b></td>
                    <td>
                        <?php
                        $aTipoConta = array(0 => "Sintética", 1 => "Analítica");
                        db_select("iTipoConta", $aTipoConta, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label class='bold' for="cbxSistema">Sistema:</label>
                    </td>
                    <td>
                        <select id="cbxSistema">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr id="trIndicadorSuperavit" style="display: none;">
                    <td nowrap="nowrap"><b>Indicador Superávit:</b></td>
                    <td>
                        <?php
                        /*
                         * Organiza um array com os valores padrão cadastrado no dicionário de dados
                         */
                        $aIndicadorSuperavit = getValoresPadroesCampo("c60_identificadorfinanceiro");
                        $aRecordSuperavit    = array();
                        foreach ($aIndicadorSuperavit as $sSigla => $sDescricao) {
                            $aRecordSuperavit[$sSigla] = "{$sSigla} - {$sDescricao}";
                        }
                        db_select("sIndicadorSuperavit", $aRecordSuperavit, true, $db_opcao);
                        ?>
                    </td>
                </tr>
                <tr id="trDetalhamentoSistema" style="display:none;">
                    <td nowrap="nowrap">
                        <?php
                        db_ancora("<b>Detalhamento do Sistema:</b>", "js_lookupDetalhamentoSistema(true)", $db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input("iDetalhamentoSistema", 5, false, 3, "text", $db_opcao, "onchange='js_lookupDetalhamentoSistema(false);'");
                        db_input("sDescricaoDetalhamentoSistema", 35, false, 3, "text", 3, "", "","", "width:81%;");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset>
                            <legend><b>Funcionamento</b></legend>
                            <?php
                            db_textarea("sFuncionamento", 3, 65, false, true, 'text', $db_opcao);
                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset>
                            <legend><b>Função</b></legend>
                            <?php
                            db_textarea("sFuncao", 3, 65, false, true, 'text', $db_opcao);
                            ?>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </fieldset>
        <br>
        <input type="button" name="btnIncluir" id="btnIncluir" value="Salvar"  />
        <input type="button" name="btnPesquisar" id="btnPesquisar" value="Pesquisar"  />

    </center>
</form>
</body>
</html>

<script>

  var sUrlRPC = "con4_conplanoPCASP.RPC.php";

    (function () {
        new AjaxRequest(
          sUrlRPC,
          {'exec' : 'buscarSistema'},
          function (retorno, erro) {

            if (erro) {
                alert("Erro ao buscar os tipos de sistemas.");
                return false;
            }

            retorno.sistemas.each(function(sistema){
                var descricao = sistema.sigla + ' - ' + sistema.descricao.urlDecode();
                $('cbxSistema').add(new Option(descricao, sistema.sequencial));
            });
          }
        ).setMessage('Aguarde, buscando sistemas...').execute();
    })();


  $("btnPesquisar").observe("click", function () {

    var sUrl = 'func_conplanogeral.php?funcao_js=parent.js_preenchePlano|c60_codcon';
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_conta','db_iframe_conta',sUrl,'Pesquisa',true,'0');
  });

  var detalhamentoSistemaInicial;

  function js_preenchePlano(iCodigoConta) {

    db_iframe_conta.hide();
    var oUrl = js_urlToObject(window.location.search);
    if (oUrl.db_opcao == 1) {
      return true;
    }
    js_divCarregando("Aguarde, carregando plano de contas...", "msgBox");
    var oParam          = new Object();
    oParam.exec         = "getPlanoContasPCASP";
    oParam.iCodigoConta = iCodigoConta;

    var oAjax = new Ajax.Request(sUrlRPC,
      {method:'post',
        parameters:'json='+Object.toJSON(oParam),
        onComplete: js_preenchePlanoConta
      }
    );
  }

  function js_preenchePlanoConta(oAjax) {

    js_removeObj("msgBox");
    var oRetorno = JSON.parse(oAjax.responseText);
    $("iCodigoConta").value                 = oRetorno.iCodigoConta;
    document.form1.c90_estrutcontabil.value = oRetorno.sEstrutural;
    var aFields = $('form1').elements;
    for (var iField = 0; iField < aFields.length; iField++) {

      with (aFields[iField]) {

        if (oRetorno.dados[id]) {

          if (oRetorno.dados[id].urlDecode) {
            oRetorno.dados[id] = oRetorno.dados[id].urlDecode();
          }
          value = oRetorno.dados[id];
        }
      }
    }
    $("c90_estrutcontabil").disabled = true;
    js_lookupDetalhamentoSistema(false);
    $('iTipoConta').value = oRetorno.dados.iTipoConta;

    $('cbxSistema').onchange();

    var lAbaReduzidos     = oRetorno.dados.iTipoConta == 0?false:true;
    js_liberaAbasPlano(oRetorno.dados.iCodigoConta, lAbaReduzidos);
    detalhamentoSistemaInicial = document.getElementById("iDetalhamentoSistema").value;


  }

  /**
   * Atualiza a conta removendo o indicador de superávit
   */
  function removerIndicadorsuperavit() {

    new AjaxRequest(sUrlRPC, { exec : "removerIndicadorSuperavit", iCodigoConta : $("iCodigoConta").value }, function(oResponse, lError) {

      if (lError) {
        return alert(oResponse.message.urlDecode());
      }
    }).setMessage("Aguarde, alterando o indicador de superávit.")
      .execute();
  }


    /**
   * Valida o indicador de superávit
   * Caso o indicador seja diferente de não se aplica, deve ter algum reduzido cadastrado
   */
  $('sIndicadorSuperavit').observe('change', function() {

if ($F("cbxSistema") == 2 && this.value != 'N') {

  var oGridReduzidos = (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_reduzido.oGridReduzido;

  if (oGridReduzidos == undefined || oGridReduzidos.getRows().length < 1) {

    this.value = 'N';
    alert('Somente contas analíticas possuem Indicador de Superávit. É necessário cadastrar um reduzido para a conta.');

    if ($("iCodigoConta").value) {
      removerIndicadorsuperavit();
    }
  }
} else if ($("iCodigoConta").value) {
  removerIndicadorsuperavit();
}
});


  $("btnIncluir").observe("click", function() {

    var iCodigoConta         = $("iCodigoConta").value;
    var sEstrutural          = document.form1.c90_estrutcontabil.value;
    var sTitulo              = encodeURIComponent(tagString($("sTitulo").value));
    var iNaturezaSaldo       = $("iNaturezaSaldo").value;
    var sFuncionamento       = encodeURIComponent(tagString($("sFuncionamento").value));
    var iSistemaConta        = $F("cbxSistema");
    var iDetalhamentoSistema = $("iDetalhamentoSistema").value;
    var sSuperavitFinanceiro = 'N';
    var iClassificacao       = 1;
    var bSaldoContinuo       = $F('bSaldoContinuo');
    var iTipoConta           = $("iTipoConta").value;
    var iContaCorrente       = '';

    var sFuncao              = encodeURIComponent(tagString($("sFuncao").value));

    /**
     * Validações dos campos
     */
    if (sEstrutural == "") {

      alert("Informe a estrutura contábil do plano de contas.");
      return false;
    }

    if (sEstrutural.substring(0, 1) == "0") {

      alert("O estrutural contábil do plano de contas não pode iniciar com zero.");
      return false;
    }

    if (sTitulo == "") {

      alert("Informe o título do plano de contas.");
      return false;
    }

    if ($("sTitulo").value.length > 50) {

      alert("O Título do plano de contas excede o tamanho máximo de caracteres (50).");
      return false;
    }

    if (iSistemaConta == "") {

      alert("Informe o sistema de contas.");
      return false;
    }

    /*
     * Valida se o sistema de contas é "Informações Patrimoniais - 2" caso seja, o indicador
     * de superavit não pode ser "NÃO SE APLICA"
     */
    if (iSistemaConta == 2) {

      sSuperavitFinanceiro = $("sIndicadorSuperavit").value;
    } else {
      iDetalhamentoSistema = "0";
    }

    let grupoEstrutural = parseInt(sEstrutural.charAt(0));
    let gruposBloqueados = [2, 3, 4, 5, 6, 7, 8];
    let descricaoDetalhamento = document.getElementById("sDescricaoDetalhamentoSistema");

    if (parseInt(detalhamentoSistemaInicial) !== 6 && parseInt(gruposBloqueados.indexOf(grupoEstrutural)) >= 0 && parseInt(iDetalhamentoSistema) === 6) {
      alert("Não é possível utilizar o Detalhamento do Sistema \"" + descricaoDetalhamento.value.toLowerCase().ucFirst() + "\" em contas do grupo "+grupoEstrutural + ".");
      return false;
    }

    js_divCarregando("Cadastrando plano de contas, aguarde...", "msgBox");
    var oParam                  = {};
    oParam.exec                 = "salvarPlanoConta";
    oParam.iCodigoConta         = iCodigoConta;
    oParam.sEstrutural          = sEstrutural;
    oParam.sTitulo              = sTitulo;
    oParam.iNaturezaSaldo       = iNaturezaSaldo;
    oParam.sFuncionamento       = sFuncionamento;
    oParam.iSistemaConta        = iSistemaConta;
    oParam.sIndicadorSuperavit  = sSuperavitFinanceiro;
    oParam.iDetalhamentoSistema = iDetalhamentoSistema;
    oParam.iClassificacao       = 1;
    oParam.iContaBancaria       = null;
    oParam.iTipoConta           = iTipoConta;
    oParam.sFuncao              = sFuncao;
    oParam.bSaldoContinuo       = bSaldoContinuo;
    oParam.iContaCorrente       = '';

    var oAjax                   = new Ajax.Request(sUrlRPC,
      {method:'post',
        parameters:'json='+Object.toJSON(oParam),
        onComplete: js_retornoSalvarPlanoConta
      }
    );

  });

  function js_retornoSalvarPlanoConta(oAjax) {

    js_removeObj("msgBox");
    var oRetorno = JSON.parse(oAjax.responseText);
    alert(oRetorno.message.urlDecode());

    if (oRetorno.status == 1) {

      $("iCodigoConta").value                    = oRetorno.iCodigoConta;

      if ($F("iTipoConta") == 1) {

        alert("Aba 'Reduzidos' liberada.");
        parent.mo_camada('reduzido');
        js_liberaAbasPlano(oRetorno.iCodigoConta, true);
      }
    }
  }

  /**
   * Valida o Subsistema de contas escolhido e mostra a TR do indicador do superavit.
   * Isso só acontecerá caso o sub-sistema de contas escolhidos seja 2.
   */
   $("cbxSistema").onchange = function() {

        if ($F("cbxSistema") == 2 ) {

          $("trIndicadorSuperavit").style.display  = '';
          $("trDetalhamentoSistema").style.display = '';

          if (!$("iCodigoConta").value) {
            $('sIndicadorSuperavit').value = 'N';
          }
        } else {

          $('sIndicadorSuperavit').value = 'N';

          $("trIndicadorSuperavit").style.display  = 'none';
          $("trDetalhamentoSistema").style.display = 'none';
        }
  }

  /**
   * Funções de Pesquisa da Classificação do Sistema
   */

  /**
   * Funções de Pesquisa do Detalhamento do Sistema de contas
   */
  function js_lookupDetalhamentoSistema(lMostra) {

    if (lMostra == true) {
      var sUrl = 'func_consistema.php?funcao_js=parent.js_mostraDetalhamentoSistema|c52_codsis|c52_descr';
      js_OpenJanelaIframe('CurrentWindow.corpo.iframe_conta','db_iframe_consistemaconta',sUrl,'Pesquisa',true,'0');
    } else {
      if($("iDetalhamentoSistema").value != ''){
        var sUrl = 'func_consistema.php?pesquisa_chave='+$("iDetalhamentoSistema").value+'&funcao_js=parent.js_completaDetalhamentoSistema';
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_conta','db_iframe_consistemaconta',sUrl,'Pesquisa',false);
      } else {
        $("sDescricaoDetalhamentoSistema").value = '';
      }
    }
  }
  function js_mostraDetalhamentoSistema(iCodigo, sDescricao) {

    $("iDetalhamentoSistema").value          = iCodigo;
    $("sDescricaoDetalhamentoSistema").value = sDescricao;
    db_iframe_consistemaconta.hide();
  }
  function js_completaDetalhamentoSistema(sDescricao, lErro) {

    if (!lErro) {
      $("sDescricaoDetalhamentoSistema").value = sDescricao;
    } else {
      $("iDetalhamentoSistema").value          = '';
      $("sDescricaoDetalhamentoSistema").value = sDescricao;
    }
  }

  function js_liberaAbasPlano(iCodigoConta, lAbaReduzidos) {

    parent.document.formaba.reduzido.disabled  = true;
    if (lAbaReduzidos) {

      parent.document.formaba.contacorrente.disabled = false;
      parent.iframe_contacorrente.location.href      = "con1_planocontapcasp006.php?iCodigoConta="+iCodigoConta;

      parent.document.formaba.reduzido.disabled  = false;
      parent.iframe_reduzido.location.href       = "con1_planocontapcasp004.php?iCodigoConta="+iCodigoConta;
      parent.iframe_reduzido.verificarSistemaDeContas();

    }

    parent.document.formaba.vinculo.disabled = false;
    parent.iframe_vinculo.location.href      = "con1_planocontapcasp005.php?iCodigoConta="+iCodigoConta;


  }

  js_main = function() {

    new MaskedInput("#c90_estrutcontabil",
      $F('mascara'),
      {placeholder:"0"}
    );

    var oUrl = js_urlToObject(window.location.search);

    switch (oUrl.db_opcao) {

      case '3':

        $("btnPesquisar").click();
        $('btnIncluir').value='excluir';
        $('btnIncluir').stopObserving('click');
        $('btnIncluir').observe('click', function() {
          js_removerConta();
        });

        break;

      case '2':
        $("btnPesquisar").click();
        break;
    }
  }


  $('iTipoConta').observe('change', function() {

    $("trIndicadorSuperavit").style.display  = 'none';
    $("trDetalhamentoSistema").style.display  = 'none';
    $('cbxSistema').options[1].disabled = false;
    $('cbxSistema').value = '';
    $('sIndicadorSuperavit').value = 'N';
    $('iDetalhamentoSistema').value = '';
    $('sDescricaoDetalhamentoSistema').value = '';

    if ($F("iTipoConta") == 1) {
      $('cbxSistema').options[1].disabled = true;
    }
  });




  /**
   * Função que remove uma conta bancária do sistema
   */
  function js_removerConta() {

    var oParam          = new Object();
    oParam.exec         = 'removerConta';
    oParam.iCodigoConta =  $F('iCodigoConta');
    js_divCarregando('Aguarde. excluindo dados da Conta..', 'msgBox');
    var oAjax           = new Ajax.Request(sUrlRPC,
      {method:'post',
        parameters:'json='+Object.toJSON(oParam),
        onComplete: js_retornoRemoverConta
      }
    );
  }
  function js_retornoRemoverConta(oAjax) {

    js_removeObj('msgBox');
    var oRetorno = JSON.parse(oAjax.responseText);
    if (oRetorno.status == 2) {
      alert(oRetorno.message.urlDecode());
    } else {

      alert('Conta excluida com sucesso!');
      $('form1').reset();
      $("btnPesquisar").click();
    }
  }

  js_main();
</script>
