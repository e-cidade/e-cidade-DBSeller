<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidadedbseller.com.br
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

//MODULO: caixa
$clsaltes->rotulo->label();
$oRotulo = new rotulocampo();
$oRotulo->label("k103_contrapartida");
$oRotulo->label("k109_contaextra");
$oRotulo->label("coddepto");

$displayDepartamentos = "none";
if (ParametroCaixa::utilizaFiltroContasDepartamento()) {
    $displayDepartamentos = "true";
}
?>

<style>
  .fieldsetinterno {

    border: 0px;
    border-top: 2px groove white;
  }
</style>


<form name="form1" method="post" action="">
  <input type="hidden" value="" id="departamentos" name="departamentos"/>
  <table class="form-container">
    <tr>
      <td>
        <fieldset>
          <legend><b>Cadastro de Contas </b></legend>
          <fieldset class="fieldsetinterno">
            <legend>&nbsp;Identificação&nbsp;</legend>
            <table>
              <tr>
                <td nowrap title="<?= $Tk13_descr ?>">
                  <?php
                    if ($db_opcao == "2") {
                        db_ancora($Lk13_descr, "js_contas();", 3);
                    } else {
                        db_ancora($Lk13_descr, "js_contas();", $db_opcao);
                    }
                    ?>
                </td>
                <td>
                  <?php
                    db_input('k13_reduz', 8, "", true, 'text', 3);
                    db_input('k13_descr', 40, $Ik13_descr, true, 'text', $db_opcao);
                    ?>
                </td>
              </tr>
              <tr>
                <td nowrap title="<?= $Tk13_ident ?>">
                  <?= $Lk13_ident ?>
                </td>
                <td>
                  <?php
                    db_input('k13_ident', 15, $Ik13_ident, true, 'text', $db_opcao, "")
                    ?>
                </td>
              </tr>
              <tr>
                <td nowrap title="<?= $Tk13_limite ?>">
                  <?= $Lk13_limite ?>
                </td>
                <td>
                  <?php
                    if (empty($k13_limite)) {
                        $k13_limite = "//";
                    }
                    list($k13_limite_dia, $k13_limite_mes, $k13_limite_ano) = split("/", $k13_limite);
                    db_inputdata('k13_limite', $k13_limite_dia, $k13_limite_mes, $k13_limite_ano, true, 'text', $db_opcao, "");
                    ?>
                </td>
              </tr>
            </table>
          </fieldset>

          <fieldset class="fieldsetinterno">
            <legend>&nbsp;Implantação do saldo&nbsp;</legend>
            <table>
              <tr>
                <td nowrap title="<?= $Tk13_dtimplantacao ?>">
                  <?= $Lk13_dtimplantacao ?>
                </td>
                <td>
                  <?php
                    if (empty($k13_dtimplantacao)) {
                        $k13_dtimplantacao = "//";
                    }
                    list($k13_dtimplantacao_dia, $k13_dtimplantacao_mes, $k13_dtimplantacao_ano) = split("/", $k13_dtimplantacao);
                    db_inputdata(
                        'k13_dtimplantacao',
                        $k13_dtimplantacao_dia,
                        $k13_dtimplantacao_mes,
                        $k13_dtimplantacao_ano,
                        true,
                        'text',
                        $db_opcao
                    );
                    ?>
                </td>
              </tr>
              <tr>
                <td nowrap title="<?= $Tk13_saldo ?>"><?= $Lk13_saldo ?> </td>
                <td>
                  <?php
                    if (isset($k13_saldo) && $k13_saldo != "") {
                        $k13_saldo = str_replace(",", ".", $k13_saldo);
                    }
                    db_input('k13_saldo', 15, $Ik13_saldo, true, 'text', $db_opcao, "onBlur=this.value=this.value.replace(',','.');")
                    ?>
                </td>
              </tr>
            </table>
          </fieldset>

          <fieldset class="fieldsetinterno">
            <legend>&nbsp;Saldo Atualizado&nbsp;</legend>
            <table>
              <tr>
                <td nowrap title="<?= $Tk13_datvlr ?>">
                  <?= $Lk13_datvlr ?>
                </td>
                <td>
                  <?php
                    if (empty($k13_datvlr)) {
                        $k13_datvlr = "//";
                    }
                    list($k13_datvlr_dia, $k13_datvlr_mes, $k13_datvlr_ano) = split("/", $k13_datvlr);
                    db_inputdata('k13_datvlr', $k13_datvlr_dia, $k13_datvlr_mes, $k13_datvlr_ano, true, 'text', 3, "");
                    ?>
                </td>
              </tr>
              <tr>
                <td nowrap title="<?= $Tk13_vlratu ?>"><?= $Lk13_vlratu ?>
                </td>
                <td>
                  <?php
                    db_input('k13_vlratu', 15, $Ik13_vlratu, true, 'text', 3, "")
                    ?>
                </td>
              </tr>
            </table>
          </fieldset>

          <fieldset class="fieldsetinterno">
            <legend>&nbsp;Outros dados&nbsp;</legend>
            <table>
              <tr>
                <td>
                  <?php
                    db_ancora($Lk103_contrapartida, "js_saltes(true);", $db_opcao);
                    ?>
                </td>
                <td>
                  <?php
                    db_input('k103_contrapartida', 8, $Ik103_contrapartida, true, 'text', $db_opcao, "onchange='js_saltes(false)'");
                    db_input('k103_descr', 40, $Ik13_descr, true, 'text', 3);
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php
                    db_ancora($Lk109_contaextra, "js_saltes2(true);", $db_opcao);
                    ?>
                </td>
                <td>
                  <?php
                    db_input('k109_saltesextra', 8, $Ik109_contaextra, true, 'text', $db_opcao, "onchange='js_saltes2(false)'");
                    db_input('k103_descrextra', 40, $Ik13_descr, true, 'text', 3);
                    ?>
                </td>
              </tr>

            </table>
          </fieldset>

          <fieldset style="display: <?=$displayDepartamentos?>;">
            <legend>Departamentos Vinculados</legend>
            <table>
              <tr>
                <td>
                  <?php
                    db_ancora("Departamento", "pesquisaDepartamento(true);", $db_opcao);
                    ?>
                </td>
                <td>
                  <?php
                    db_input('coddepto', 8, 1, true, 'text', $db_opcao, "onchange='pesquisaDepartamento(false)'");
                    db_input('descrdepto', 40, 3, true, 'text', 3);
                    ?>
                  <input type="button"
                         name="btnAdicionarDepartamento"
                         id="btnAdicionarDepartamento"
                         value="Adicionar"
                         <?php echo (($db_opcao == 3 || $db_opcao == 33)?"disabled":"");?> />
                </td>
              </tr>
              <tr>
                <td colspan="2" id="ctnDepartamentos"></td>
              </tr>
            </table>
          </fieldset>

      </td>
    </tr>
  </table>
  </fieldset>

  <input name="submit"
         type="submit"
         id="submit"
         value="<?=($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
         <?=($db_botao == false ? "disabled" : "") ?>
         onclick="return carregarDepartamentos()" />

  <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">

</form>

<script>

  const db_opcao = <?=$db_opcao?>;

  var sDisabled = "";
  if (db_opcao == 3 || db_opcao == 33) {
    sDisabled = "disabled";
  }

  oGridDepartamentos = new DBGrid("gridDepartamentos");
  oGridDepartamentos.nameInstance = "oGridDepartamentos";
  oGridDepartamentos.setCellAlign(new Array("center", "left", "center", "center"));
  oGridDepartamentos.setCellWidth(new Array("15%","55%","15%","15%"));
  oGridDepartamentos.setHeader(new Array("Código", "Descrição", "Principal", "Ação"));
  oGridDepartamentos.show($('ctnDepartamentos'));

  function adicionarDepartamento(codigo, descricao, principal) {

      if (codigo == "") {
        alert("Departamento não informado");
        return false;
      }

      for (var iIndice=0; iIndice < aDepartamentos.length; iIndice++) {
        if ( !aDepartamentos[iIndice] ) {
          continue;
        }

        if (codigo == aDepartamentos[iIndice].codigo) {
          alert("Departamento "+codigo+" - "+descricao+" já adicionado");

          $("coddepto").value = "";
          $("descrdepto").value = "";

          return false;
        }
      }

      var departamento = new Object();
      departamento.codigo    = codigo;
      departamento.descricao = encodeURIComponent(descricao);
      departamento.principal = principal;
      aDepartamentos.push(departamento);

      $("coddepto").value = "";
      $("descrdepto").value = "";

      recarregaGrid();
      return true;
  }

  function recarregaGrid() {

      oGridDepartamentos.clearAll(true);

      for (var iIndice=0; iIndice < aDepartamentos.length; iIndice++) {

        if (!aDepartamentos[iIndice]) {
          continue;
        }

        var departamento = aDepartamentos[iIndice];
        sInputSelect  = "<input type='radio' ";
        sInputSelect += "       name='departamentoPrincipal'";
        sInputSelect += "       id='departamentoPrincipal'";
        if (departamento.principal == "t") {
          sInputSelect += " checked ";
        }
        sInputSelect += sDisabled;
        sInputSelect += "       onclick='alterarDepartamentoPrincipal("+departamento.codigo+")' > ";

        sButtonRemover  = "<input type='button' ";
        sButtonRemover += "       name='btnRemoverDepartamento' ";
        sButtonRemover += "       id='btnRemoverDepartamento' ";
        sButtonRemover += "       value='Remover' ";
        sButtonRemover += sDisabled;
        sButtonRemover += "       onclick='removerDepartamento("+departamento.codigo+")' > ";

        var aLinha = [];
        aLinha[0] = departamento.codigo;
        aLinha[1] = decodeURIComponent(departamento.descricao);
        aLinha[2] = sInputSelect;
        aLinha[3] = sButtonRemover;
        oGridDepartamentos.addRow(aLinha);

      }
      oGridDepartamentos.renderRows();
      return true;
  }


  function alterarDepartamentoPrincipal(departamento) {

      for (var iIndice=0; iIndice < aDepartamentos.length; iIndice++) {
        if (!aDepartamentos[iIndice]) {
          continue;
        }

        aDepartamentos[iIndice].principal = "f";
        if (departamento == aDepartamentos[iIndice].codigo) {
            aDepartamentos[iIndice].principal = "t";
        }

      }
      return true;
  }

  function removerDepartamento(departamento) {

      for (var iIndice=0; iIndice < aDepartamentos.length; iIndice++) {

            if (!aDepartamentos[iIndice]) {
              continue;
            }

            if (departamento == aDepartamentos[iIndice].codigo) {
                delete(aDepartamentos[iIndice]);
            }

      }
      aDepartamentos = aDepartamentos.flat();

      recarregaGrid();
      return true;
  }


  $('btnAdicionarDepartamento').observe("click", function() {
      adicionarDepartamento($F("coddepto"), $F("descrdepto"), false);
  });

  /*
   * Carregamos os departamentos vinculados a conta
   */
  function carregarDepartamentosCadastrados() {

    var oReduzido = $('k13_reduz');
    if (oReduzido.value != "") {

      new AjaxRequest(
        'cai4_saltesRPC.php',
        {exec:'getDepartamentosContaBancaria', k13_conta: oReduzido.value},
        function(oRetorno, lErro) {

          aDepartamentos = [];

          oRetorno.departamentos.each(
            function (registro) {
              adicionarDepartamento(registro.k212_departamento,registro.descrdepto.urlDecode(),registro.k212_principal);
            });
          recarregaGrid();
        }
      ).setMessage("Aguarde, carregando departamentos...").execute();
    }
  }
  carregarDepartamentosCadastrados();

  /*
   * Função que carrega e organiza os departamentos para realizar a inclusao ou alteracao da conta
   */
  function carregarDepartamentos() {

      $("departamentos").value = JSON.stringify(aDepartamentos);
      return true;
  }


  function js_pesquisa() {
    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_saltes', 'func_saltes.php?funcao_js=parent.js_preenche|k13_conta&sem_filtro_departamento=1', 'Pesquisa', true);
  }

  function js_preenche(chave) {
    db_iframe_saltes.hide();
    <?php
    echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
    ?>
  }

  function js_contas() {
    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_saltes', 'func_saltes_contas001.php?funcao_js=parent.js_preenche_conta|c62_reduz|c60_descr', 'Pesquisa', true);
  }

  function js_preenche_conta(chave1, chave2) {
    db_iframe_saltes.hide();
    document.form1.k13_reduz.value = chave1;
    document.form1.k13_descr.value = chave2.substring(0, 40);
  }

  function js_saltes(lMostra) {

    if (lMostra) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_contrapartida',
        'func_saltes.php?funcao_js=parent.js_contrapartida|k13_conta|k13_descr',
        'Pesquisa de Contas', true);
    } else {

      var iContrapartida = document.form1.k103_contrapartida.value;
      if (iContrapartida != '') {

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_contrapartida',
          'func_saltes.php?funcao_js=parent.js_contrapartida2&pesquisa_chave=' + iContrapartida, 'Pesquisa de contas', false);
      } else {

        document.form1.k103_descr.value = '';
      }
    }
  }

  function js_contrapartida(chave1, chave2) {

    db_iframe_contrapartida.hide();
    document.form1.k103_contrapartida.value = chave1;
    document.form1.k103_descr.value = chave2;
  }

  function js_contrapartida2(sRetorno, lErro) {

    db_iframe_contrapartida.hide();
    if (!lErro) {
      document.form1.k103_descr.value = sRetorno;
    } else {

      document.form1.k103_descr.value = sRetorno;
      document.form1.k103_contrapartida.value = '';
    }
  }

  function js_saltes2(lMostra) {

    if (lMostra) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_contrapartida',
        'func_saltes.php?funcao_js=parent.js_saltesextra|k13_conta|k13_descr',
        'Pesquisa de Contas', true);
    } else {

      var iContrapartida = document.form1.k109_saltesextra.value;
      if (iContrapartida != '') {

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_contrapartida',
          'func_saltes.php?funcao_js=parent.js_saltesextra2&pesquisa_chave=' + iContrapartida, 'Pesquisa de contas', false);
      } else {

        document.form1.k103_descrextra.value = '';
      }
    }
  }

  function js_saltesextra(chave1, chave2) {

    db_iframe_contrapartida.hide();
    document.form1.k109_saltesextra.value = chave1;
    document.form1.k103_descrextra.value = chave2;
  }

  function js_saltesextra2(sRetorno, lErro) {

    db_iframe_contrapartida.hide();
    if (!lErro) {
      document.form1.k103_descrextra.value = sRetorno;
    } else {

      document.form1.k103_descrextra.value = sRetorno;
      document.form1.k109_saltesextra.value = '';
    }
  }


  function pesquisaDepartamento(lMostra) {
     if (lMostra) {
       js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_departamento',
                           'func_db_depart.php?funcao_js=parent.retornoPequisaDepartamento|coddepto|descrdepto',
                           'Pesquisa Departamento',
                           true);
     } else {

       if ($F("coddepto") != '') {
         js_OpenJanelaIframe('CurrentWindow.corpo',
                            'db_iframe_departamento',
                            'func_db_depart.php?funcao_js=parent.retornoPequisaDepartamento2&pesquisa_chave='+$F("coddepto"),
                            'Pesquisa Departamento',
                            false);
       } else {
         document.form1.descrdepto.value = '';
       }
     }
  }

  function retornoPequisaDepartamento(codigo, descricao) {
    $("coddepto").value = codigo;
    $("descrdepto").value = descricao;
    db_iframe_departamento.hide();
  }

  function retornoPequisaDepartamento2(descricao, lErro) {
    if (!lErro) {
      $("descrdepto").value = descricao;
    } else {
      $("descrdepto").value = "Departamento "+$F("coddepto")+" não encontrado";
      $("coddepto").value = '';
    }
    db_iframe_departamento.hide();
  }

  function setLabelWidth() {

    var aRows = $$('.fieldsetinterno table td:first-child');
    var iMaxHeigth = 0;
    aRows.each(function(oLinha, id) {

      if (oLinha.scrollWidth > iMaxHeigth) {
        iMaxHeigth = oLinha.scrollWidth;
      }
    });

    aRows.each(function(oLinha, id) {

      oLinha.style.width = iMaxHeigth;
    });
  }
  setLabelWidth();
</script>
