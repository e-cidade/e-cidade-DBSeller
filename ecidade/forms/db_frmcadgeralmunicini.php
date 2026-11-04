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

$lFisico   = true;
$lJuridico = true;
if (isset($oGet->lFisico) && trim($oGet->lFisico) != '' && $oGet->lFisico == 'false') {
  $lFisico = false;
}
if (isset($oGet->lJuridico) && trim($oGet->lJuridico) != '' && $oGet->lJuridico == 'false') {
  $lJuridico = false;
}
?>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="estilos.css" />
  <link rel="stylesheet" type="text/css" href="estilos/grid.style.css" />
  <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
  <script type="text/javascript" src="scripts/prototype.js"></script>
  <script type="text/javascript" src="scripts/scripts.js"></script>
  <script type="text/javascript" src="scripts/strings.js"></script>
  <script type="text/javascript" src="scripts/widgets/dbtextField.widget.js"></script>
  <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
  <script type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
  <script type="text/javascript" src="scripts/widgets/dbcomboBox.widget.js"></script>
  <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
  <script type="text/javascript" src="scripts/classes/dbViewCadEndereco.classe.js"></script>
</head>

<script type="text/javascript">
  var lPermissaoCnpjZerado = false;
  var lPermissaoCpfZerado = false;

  function js_importaCadastroCidadao() {

    js_OpenJanelaIframe('', 'db_iframe_cidadao',
      'func_cidadaovinculos.php?funcao_js=parent.js_mostracidadao1|0|4&liberado=true&ativo=true&vinculocgm=false',
      'Pesquisa', true);
  }

  function js_mostracidadao1(chave1, chave2) {

    $('ov02_sequencial').value = chave1;
    if (chave2.length == 11) {
      $('cpf').value = chave2;
    } else if (chave2.length == 14) {
      $('cnpj').value = chave2;
    }

    db_iframe_cidadao.hide();

    js_confirma();
  }

  function js_verifica() {
    if ($F('cnpj') == "" && $F('cpf') == "") {
      alert('usuário:\n\nNenhum CPF ou CNPJ informado!\n\n');
      return false;
    } else {
      return true;
    }
    return false;
  }

  function js_confirma() {

    if ($('cpf') && $F('cpf') != "") {
      if (!js_verificaCGCCPF($('cpf'))) {
        return false
      }
    } else if ($('cnpj') && $F('cnpj') != "") {
      if (!js_verificaCGCCPF($('cnpj'))) {
        return false
      }
    } else {
      alert("usuário:\n\nInforme o CPF ou CNPJ.\n\nAdministrador:");
      return false;
    }

    if ($('cpf')) {

      if ($F('cpf') == '11111111111' || $F('cpf') == '22222222222' || $F('cpf') == '33333333333' ||
        $F('cpf') == '44444444444' || $F('cpf') == '55555555555' || $F('cpf') == '66666666666' ||
        $F('cpf') == '77777777777' || $F('cpf') == '88888888888' || $F('cpf') == '99999999999') {

        alert('O número do CPF informado está incorreto');
        return false;
      }

      if ($F('cpf') == '00000000000' && !lPermissaoCpfZerado) {

        alert('usuário:\n\n Você não tem permissão de inserir CPF zerado!\n\n');
        $('cpf').value = '';
        $('cpf').focus();
        return false;
      }
    }

    if ($('cnpj') && $F('cnpj') == '00000000000000' && !lPermissaoCnpjZerado) {
      alert('usuário:\n\n Você não tem permissão de inserir CNPJ zerado!\n\n');
      $('cnpj').value = '';
      $('cnpj').focus();
      return false;
    }

    if ($('cpf') && $F('cpf') == '00000000000' && lPermissaoCnpjZerado) {
      document.form1.submit();
      return false;
    }

    if ($('cnpj') && $F('cnpj') == '00000000000000' && lPermissaoCnpjZerado) {
      document.form1.submit();
      return false;
    }

    var oPesquisa = new Object();
    oPesquisa.exec = 'findCpfCnpj';
    oPesquisa.iCpfCnpj = '';
    if ($('cpf')) {

      oPesquisa.iCpfCnpj = $F('cpf');
    }

    if ($F('cnpj').trim() != "") {
      oPesquisa.iCpfCnpj = $F('cnpj');
    }
    var msgDiv = "Aguarde verificando CPF/CNPJ.";

    js_divCarregando(msgDiv, 'msgBox');

    sUrlRpc = "prot1_cadgeralmunic.RPC.php";

    var oAjax = new Ajax.Request(
      sUrlRpc, {
        parameters: 'json=' + Object.toJSON(oPesquisa),
        method: 'post',
        onComplete: retornofindCpfCnpj
      }

    );
  }

  function retornofindCpfCnpj(oAjax) {

    js_removeObj("msgBox");

    var oRetorno = JSON.parse(oAjax.responseText);
    var sExpReg = new RegExp('\\\\n', 'g');

    if (oRetorno.z01_numcgm == false) {
      document.form1.submit();
    } else {
      var strMessage = "usuário:\n\n Cnpj/Cpf já cadastrado para o CGM = " + oRetorno.z01_numcgm;
      strMessage += "\n\nDeseja visualizar os dados? ";
      if (confirm(strMessage)) {
        $('cpf').value = '';
        $('cnpj').value = '';
        $('cpf').focus();

        js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'prot3_consultacgmnovo002.php?numcgm=' + oRetorno.z01_numcgm, 'Pesquisa', true);

        db_janela_Cgm.liberarJanBTFechar(false);
        db_janela_Cgm.liberarJanBTMinimizar(false);
        return false;
      } else {
        $('cpf').value = '';
        $('cnpj').value = '';
        $('cpf').focus();
        return false;
      }

    }

  }
</script>
<form name="form1" method="post" action="">
  <div class="container">
    <fieldset>
      <legend><b>Cadastro Geral do Município</b></legend>

      <table cellspacing="3" cellpadding="0.9" width="100%">
        <?php if (isset($flagAlteracao) && $flagAlteracao) { ?>
          <tr>
            <td colspan="2" style="text-align:center; padding: 8px;">
              <span style="color:red"><b>CGM selecionado não possui CPF/CNPJ válido. Por favor informe o dado correto.</b></span>
            </td>
          </tr>
        <?php } ?>

        <tr <?= $lFisico ? '' : 'style="display:none;"' ?>>
          <td title="CPF">
            <b>CPF:&nbsp;&nbsp;</b>
          </td>
          <td>
            <input style="text-align:right; width: 100%;" type="text" value="" name="cpf" id="cpf" size="18" maxlength="11" onBlur="js_verificaCGCCPF(this)" onKeyDown="return js_controla_tecla_enter(this,event);">
          </td>
        </tr>
        <tr title="CNPJ" <?= $lJuridico ? '' : 'style="display:none;"' ?>>
          <td>
            <b>CNPJ:</b>
          </td>
          <td>
            <input style="text-align:right; width: 100%;" type="text" value="" name="cnpj" id="cnpj" size="18" maxlength="14" onBlur="js_verificaCGCCPF(this)" onKeyDown="return js_controla_tecla_enter(this,event);">
          </td>
        </tr>

      </table>
    </fieldset>


    <input type="button" name="enviar" value="Confirma" onClick="js_confirma();">

    <!-- Aqui tem que validar a permissão de menu -->
    <?php
    $lPermissaoCpfZerado  = db_permissaomenu(db_getsession("DB_anousu"), 604, 3775);
    $lPermissaoCnpjZerado = db_permissaomenu(db_getsession("DB_anousu"), 604, 4459);

    $lPermissaoImportaCidadao = db_permissaomenu(db_getsession("DB_anousu"), 604, 7900);
    if ($lPermissaoImportaCidadao) {
    ?>
      <input type="button" name="enviar" value="Importar Cadastro do Cidadão" onClick="js_importaCadastroCidadao();">
    <?php
      db_input('ov02_sequencial', 10, '', 1, 'hidden', 1);
    }
    ?>
    <script type="text/javascript">
      lPermissaoCpfZerado = <?= $lPermissaoCpfZerado; ?>;
      lPermissaoCnpjZerado = <?= $lPermissaoCnpjZerado; ?>;
    </script>
  </div>
</form>
<script type="text/javascript">
  if ($('cpf')) {
    $('cpf').focus();
  } else {
    $('cnpj').focus();
  }

  function js_findCgm(chavePesquisa) {
    sUrlRpc = "prot1_cadgeralmunic.RPC.php";

    var oCgm = new Object();
    oCgm.exec = 'findCgm';
    oCgm.numcgm = chavePesquisa;

    var msgDiv = "Aguarde ...";
    js_divCarregando(msgDiv, 'msgBox');

    var oAjax = new Ajax.Request(
      sUrlRpc, {
        parameters: 'json=' + Object.toJSON(oCgm),
        method: 'post',
        onComplete: js_retornoFindCgm
      }

    );
  }

  function js_retornoFindCgm(oAjax) {

    js_removeObj('msgBox');
    var oRetorno = JSON.parse(oAjax.responseText);

    var sExpReg = new RegExp('\\\\n', 'g');

    if (oRetorno.status == 2) {

      alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
      parent.location.href = "prot1_cadgeralmunic002.php";
      return false;
    } else {

      if (oRetorno.endereco != false) {

        js_PreencheEndereco(oRetorno.endereco);
      }
      js_PreencheFormulario(oRetorno.cgm);

      if (oRetorno.tipoempresa != false) {

        js_PreencheTipoEmpresa(oRetorno.tipoempresa);
      }

      js_cgmMunicipio(oRetorno.cgmmunicipio);

      if (oRetorno.lPermissaoCidadao) {

        if (oRetorno.cidadaocgm != false) {

          var ov02_sequencial = oRetorno.cidadaocgm[0].ov03_cidadao;
          var ov02_seq = oRetorno.cidadaocgm[0].ov03_seq;
          var ov03_numcgm = oRetorno.cidadaocgm[0].ov03_numcgm;
          $('btnVincular').style.display = 'none';
          $('btnImportar').style.display = '';
          $('btnImportar').observe('click', function() {
            js_MICidadao(ov02_sequencial, ov02_seq, ov03_numcgm);
          });
        } else {

          $('btnVincular').style.display = '';
          $('btnImportar').style.display = 'none';
        }
      }
    }
  }

  if (parent.document.getElementById('cgm')) {
    parent.document.getElementById('cgm').style.display = 'none';
  }

  if (parent.document.getElementById('documentos')) {
    parent.document.getElementById('documentos').style.display = 'none';
  }

  if (parent.document.getElementById('fotos')) {
    parent.document.getElementById('fotos').style.display = 'none';
  }
</script>
