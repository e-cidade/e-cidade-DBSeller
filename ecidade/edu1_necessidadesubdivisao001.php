<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
require_once(modification("dbforms/db_funcoes.php"));
db_postmemory($_POST);

$oNecessidadeSubDivisao = new cl_necessidadesubdivisao;
$oNecessidadeSubDivisao->rotulo->label();

$db_botao = (($opcao==1)?true:false);

$db_opcao = $opcao;
if (isset($opcao) && $opcao != 1) {
  if ($opcao == 2 && !isset($chavepesquisa)) {
    $db_opcao = 22;
    $db_botao = false;
  } else if($opcao == 3 && !isset($chavepesquisa)) {
    $db_opcao = 33;
    $db_botao = false;
  }
}

try {

  if(isset($incluir)) {

    db_inicio_transacao();
      $oNecessidadeSubDivisao->ed185_necessidade = $iNecessidade;
      $oNecessidadeSubDivisao->ed185_descricao = $ed185_descricao;
      $oNecessidadeSubDivisao->incluir(null);
      if ($oNecessidadeSubDivisao->erro_status == '0') {
        throw new Exception($oNecessidadeSubDivisao->erro_msg);
      }
    db_fim_transacao();

  } else if (isset($alterar)) {

    db_inicio_transacao();
    $oNecessidadeSubDivisao->ed185_sequencial = $ed185_sequencial;
    $oNecessidadeSubDivisao->ed185_necessidade = $iNecessidade;
    $oNecessidadeSubDivisao->ed185_descricao = $ed185_descricao;
    $oNecessidadeSubDivisao->alterar($ed185_sequencial);
    if ($oNecessidadeSubDivisao->erro_status == '0') {
     throw new Exception($oNecessidadeSubDivisao->erro_msg);
    }
    db_fim_transacao();

    $db_opcao = 22;

  } else if (isset($excluir)) {

    db_inicio_transacao();
      $oNecessidadeSubDivisao->excluir($ed185_sequencial);
      if ($oNecessidadeSubDivisao->erro_status == '0') {
       throw new Exception($oNecessidadeSubDivisao->erro_msg);
      }
    db_fim_transacao();

    $db_opcao = 33;
  }

} catch (Exception $oErro) {

  db_fim_transacao(true);

  $oNecessidadeSubDivisao->erro_status = '0';
  $oNecessidadeSubDivisao->erro_msg = $oErro->getMessage();
}

if (isset($chavepesquisa)) {

  $campos = 'necessidadesubdivisao.*, necessidade.*';
  $sSql = $oNecessidadeSubDivisao->sql_query($chavepesquisa, $campos);
  $rsNecessidadeSubDivisao = db_query($sSql);
  db_fieldsmemory($rsNecessidadeSubDivisao, 0);

  $iNecessidade = $ed48_i_codigo;
  $sNecessidade = $ed48_c_descr;

  $db_opcao = $opcao;
  $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Informática Ltda - Página Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
</head>
<body >
<div class="container">
<form name="form1" method="post" action="">
<inpu type="hidden" name="opcao" id="opcao" value=<?=$opcao?> >
  <fieldset style="width: 600px;">
  <legend>
    <b><?=($db_opcao==1)?"Inclusão":(($db_opcao==2||$db_opcao==22)?"Alteração":"Exclusão")?> de Subdivisão de Deficiências / Altas Habilidades</b>
  </legend>

  <table class="form-container">

    <?php if($db_opcao!=1) { ?>
      <tr>
        <td nowrap title="Sequencial">
          <?php db_ancora("Sequencial:", 'js_pesquisaNecessidadeSubDivisao(true)', true); ?>
        </td>

        <td>
          <?php
          db_input('ed185_sequencial',10,1,true,'text',($db_opcao!=1)?1:3,"onchange='js_pesquisaNecessidadeSubDivisao(false);'");
          ?>
        </td>
      </tr>
    <?php } ?>

    <tr>
      <td title="Deficiências / Altas Habilidades">
        <?php db_ancora("Deficiências / Altas Habilidades:", 'js_pesquisaNecessidade(true)', $db_opcao); ?>
      </td>
      <td>
        <?php
        db_input("iNecessidade", 10, 1, true, "text", $db_opcao, "onchange='js_pesquisaNecessidade(false);'");
        db_input("sNecessidade", 40, "", true, "text", 3);
        ?>
      </td>
    </tr>

    <tr>
      <td>
        Descrição:
      </td>
      <td>
       <?php
       db_input('ed185_descricao', 54, 0, true, 'text', $db_opcao, "");
       ?>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <fieldset id="ctSubdivisoes" style="display: none;">
          <legend>Subdivisões Cadastradas</legend>
          <div id="container-subdivisoes"></div>
        </fieldset>
      </td>
    </tr>
  </table>
 </fieldset>
  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
         type="submit"
         id="db_opcao"
         value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
         <?=($db_botao==false?"disabled":"")?> >
  <?php if($db_opcao!=1) { ?>
  <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
  <?php } ?>
</form>

</div>

<script>
oGridDados = new DBGrid('container-subdivisoes');
oGridDados.nameInstance = 'oGridDados';
oGridDados.allowSelectColumns(false);

oGridDados.setCellWidth(new Array('15%','85%'));
oGridDados.setCellAlign(new Array('center','left'));
oGridDados.setHeader(new Array('Sequencial','Descrição'));
oGridDados.setHeight(250);
oGridDados.show($('container-subdivisoes'));

function js_pesquisaNecessidadeSubDivisao( lMostra ) {
  document.form1.db_opcao.disabled = true;
  document.form1.ed185_descricao.value = '';
  document.form1.iNecessidade.value = '';
  document.form1.sNecessidade.value = '';
  if ( lMostra ) {
    js_OpenJanelaIframe("",'db_iframe_necessidadesubdivisao','func_necessidadesubdivisao.php?funcao_js=parent.js_preencheNecessidadeSubDivisao|ed185_sequencial|ed48_i_codigo|ed48_c_descr|ed185_descricao','Pesquisa',true);
  } else {
    js_OpenJanelaIframe("",'db_iframe_necessidadesubdivisao','func_necessidadesubdivisao.php?pesquisa_chave='+document.form1.ed185_sequencial.value+'&funcao_js=parent.js_preencheNecessidadeSubDivisao1','Pesquisa',false);
  }
}

function js_preencheNecessidadeSubDivisao( sequencial, iNecessidade, sDescricao, descricaoSubdivisao ) {
  document.form1.ed185_sequencial.value = sequencial;
  document.form1.iNecessidade.value = iNecessidade;
  document.form1.sNecessidade.value  = sDescricao;
  document.form1.ed185_descricao.value  = descricaoSubdivisao;
  document.form1.db_opcao.disabled = false;
  db_iframe_necessidadesubdivisao.hide();
}

function js_preencheNecessidadeSubDivisao1( lErro, sDescricao, iNecessidade, sNecessidade  ) {
  if ( !lErro ) {
    document.form1.iNecessidade.value = iNecessidade;
    document.form1.sNecessidade.value = sNecessidade;
    document.form1.ed185_descricao.value = sDescricao;
    if(document.form1.iNecessidade.value != "") {
      document.form1.db_opcao.disabled = false;
    }
  } else {
    document.form1.ed185_sequencial.value = "";
    alert(sDescricao);
  }
}

function js_mostrarSubdivisoes(iNecessidade) {
  var oParametro          = new Object();
  oParametro.exec         = 'pesquisaSubdivisao';
  oParametro.necessidade = iNecessidade;
  oParametro.order = 'ed185_descricao';
  new Ajax.Request(
    'edu2_necessidadeEspecial.RPC.php',
    {
      method:     'post',
      parameters: 'json='+Object.toJSON(oParametro),
      onComplete: js_retornaPesquisaSubdivisao
    }
  );
}

function js_retornaPesquisaSubdivisao(oResponse) {

  var oRetorno = JSON.parse(oResponse.responseText);
  var ctSubdivisoes = document.querySelector('#ctSubdivisoes');
      ctSubdivisoes.setAttribute('style','display: none;');
      oGridDados.clearAll(true);
  if (oRetorno.status == 1) {
    ctSubdivisoes.setAttribute('style','')
    oRetorno.subdivisoes.each(function(oSubdivisao, iSeq) {
    	var aRow = new Array();
        aRow[0] = oSubdivisao.sequencial;
        aRow[1] = oSubdivisao.descricao.urlDecode();
    	oGridDados.addRow(aRow);
    })

    oGridDados.renderRows();
  }
}

function js_pesquisaNecessidade( lMostra ) {
  document.form1.db_opcao.disabled = true;
  if ( lMostra ) {
    js_OpenJanelaIframe("",'db_iframe_necessidade','func_necessidade.php?funcao_js=parent.js_preencheNecessidade|ed48_i_codigo|ed48_c_descr','Pesquisa',true);
  } else {
    js_OpenJanelaIframe("",'db_iframe_necessidade','func_necessidade.php?pesquisa_chave='+document.form1.iNecessidade.value+'&funcao_js=parent.js_preencheNecessidade1','Pesquisa',false);
  }
}

function js_preencheNecessidade( iNecessidade, sDescricao ) {
  document.form1.iNecessidade.value = iNecessidade;
  document.form1.sNecessidade.value  = sDescricao;
  document.form1.db_opcao.disabled = false;
  js_mostrarSubdivisoes(iNecessidade);
  db_iframe_necessidade.hide();
}

function js_preencheNecessidade1( sDescricao, lErro ) {
  if ( !lErro ) {
    document.form1.sNecessidade.value = sDescricao;
    if(document.form1.iNecessidade.value != "") {
      document.form1.db_opcao.disabled = false;
      js_mostrarSubdivisoes(document.form1.iNecessidade.value);
    }
  } else {
    document.form1.iNecessidade.value = "";
    document.form1.sNecessidade.value = sDescricao;
  }
}

function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_necessidadesubdivisao','func_necessidadesubdivisao.php?funcao_js=parent.js_preenchepesquisa|ed185_sequencial|ed48_i_codigo|ed48_c_descr|ed185_descricao','Pesquisa',true);
}

function js_preenchepesquisa(chave) {

  if (chave != "" ) {
    db_iframe_necessidadesubdivisao.hide();
    <?php
    echo " location.href = 'edu1_necessidadesubdivisao001.php?opcao={$opcao}&chavepesquisa='+chave";
    ?>
  }

}
</script>

<?php
db_menu();
?>
</body>
</html>
<?php

/*
 * Caso tenha sido realizada a ação de inclusao/alteracao/exclusao
 * realizamos o tratamento das mensagens e redirecionamento da pagina
 */
if(isset($incluir) || isset($alterar)) {

  if ($oNecessidadeSubDivisao->erro_status=="0") {

    db_msgbox($oNecessidadeSubDivisao->erro_msg);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if ($oNecessidadeSubDivisao->erro_campo!="") {
      echo "<script> document.form1.".$oNecessidadeSubDivisao->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$oNecessidadeSubDivisao->erro_campo.".focus();</script>";
    }

  } else {

    /*
     * Logica para controle do comportamento do programa
     *
     * Determina se ira funcionar como alteracao ou inclusao
     * Mostramos a mensagem e redirecionamos a página
     */
    $opcao = $db_opcao;
    if ($db_opcao == 22) {
      $opcao = 2;
    } else if ($db_opcao == 33) {
      $opcao = 3;
    }

    db_msgbox($oNecessidadeSubDivisao->erro_msg);
    db_redireciona("edu1_necessidadesubdivisao001.php?opcao={$opcao}");
  }

} else if(isset($excluir)) {

  /*
   * Mostramos a mensagem e redirecionamos a página
   */
  db_msgbox($oNecessidadeSubDivisao->erro_msg);
  db_redireciona("edu1_necessidadesubdivisao001.php?opcao=3");
}

/*
 * Caso a opcao seja 22 ou 33, mostramos a lookup para selecionar o registro a ser alterado/excluido
 * e habilitamos o formulario para manutenção
 */
if ($db_opcao==22 || $db_opcao==33) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
