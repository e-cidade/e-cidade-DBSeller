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
$modulos = array('0' => 'Selecionar Módulo');
$grupos = array('0' => 'Selecionar Grupo');

$clworkflow->rotulo->label();
$cltipoproc->rotulo->label();
?>
<form name="form1" method="post" action="">
<fieldset>
<legend><b>Cadastro Workflow</b></legend>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tdb112_sequencial?>">
      <?=@$Ldb112_sequencial?>
    </td>
    <td>
      <?php
        db_input('db112_sequencial', 10, $Idb112_sequencial, true, 'text', 3, "");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="Módulo">
      <b>Modulo</b>
    </td>
    <td>
      <?php
        db_select('db173_modulo', $modulos, true, 1);
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="Módulo">
      <b>Grupo do Tipo de Processo:</b>
    </td>
    <td>
      <?php
        db_select('p51_tipoprocgrupo', $grupos, true, 1);
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdb112_descricao?>">
      <?=@$Ldb112_descricao?>
    </td>
    <td>
      <?php
        db_input('db112_descricao', 80, $Idb112_descricao, true, 'text', 1, "onchange='js_valordescricao();'");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tp51_descr?>">
      <b>Descrição do Processo de Protocolo:</b>
    </td>
    <td>
      <?php
        db_input('p51_descr', 80, $Ip51_descr, true, 'text', 1, "onchange='js_valordescricao();'");
      ?>
    </td>
  </tr>
</table>
</fieldset>
<table align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <input name="salvar" id="salvar" type="button" value="salvar">
      <input name="excluir" id="excluir" type="button" value="excluir" disabled>
      <input name="limpar" id="limpar" type="button" value="limpar">
      <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
    </td>
  </tr>
</table>
</form>
<script>
const
  urlRpc = 'con1_workflow.RPC.php',
  inputSequencial = $('db112_sequencial'),
  inputDescricaoWorkflow = $('db112_descricao'),
  inputDescricaoProcesso = $('p51_descr'),
  selectModulo = $('db173_modulo'),
  selectGrupo = $('p51_tipoprocgrupo'),
  botaoSalvar = $('salvar'),
  botaoExcluir = $('excluir'),
  botaoLimpar = $('limpar');


carregaSelects();

botaoSalvar.addEventListener('click', event => {
  salvar();
});

botaoExcluir.addEventListener('click', event => {
  excluir();
});

botaoLimpar.addEventListener('click', event => {
  limpar();
});

function carregaSelects(){
  carregaSelectModulo();
  carregaSelectGrupo();
}

function carregaSelectModulo(){
  oParametros = {
    'exec' : 'getModulos'
  }

  enviar(oParametros).then(response => {
    if(response.status == 1) {
      for(var modulo of response.modulos){
        var option = document.createElement('option');
        option.value = modulo.codmod;
        option.innerText = modulo.nomemod;
        selectModulo.appendChild(option);
      }
    }
  });
}

function carregaSelectGrupo(){
  oParametros = {
    'exec' : 'getTipoProcGrupos'
  }

  enviar(oParametros).then(response => {
    if(response.status == 1) {
      for(var tipoProcGrupo of response.arrTipoGrupoProc){
        var option = document.createElement('option');
        option.value = tipoProcGrupo.p40_sequencial;
        option.innerText = tipoProcGrupo.p40_descricao;
        selectGrupo.appendChild(option);
      }
    }
  });
}

function salvar(){
  if(!!js_validarcampos()){
    oParametros = {
      'exec' : 'salvar',
      'db112_sequencial' : inputSequencial.value,
      'db112_descricao' : inputDescricaoWorkflow.value,
      'db173_modulo' : selectModulo.value,
      'p51_tipoprocgrupo' : selectGrupo.value,
      'p51_descr' : inputDescricaoProcesso.value
    }

    enviar(oParametros).then(response => {
      if(response.status == 1) {
        preencheDadosWorkflow(response.workflow);
      }
      alert(response.mensagem);
    });
  }
}

function createFormData(oParametros){
  var formData = new FormData();
  for(parametro in oParametros){
    if(oParametros[parametro] instanceof Array){
      formData.append(`${parametro}[]`, oParametros[parametro]);
    } else {
      formData.append(parametro, oParametros[parametro]);
    }
  }
  return formData;
}

function enviar(oParametros){
  var formData = createFormData(oParametros);

  return HttpClient.post(urlRpc, {body: formData});
}

function preencheDadosWorkflow(workflow){
  inputSequencial.value = workflow.db112_sequencial;
  inputDescricaoWorkflow.value = workflow.db112_descricao;
  inputDescricaoProcesso.value = workflow.p51_descr;
  selectModulo.value = workflow.db173_modulo;
  selectGrupo.value = workflow.p51_tipoprocgrupo;
  habilitaExcluir();
  js_db_libera();
}

function habilitaExcluir(){
  botaoExcluir.disabled = false;
}

function excluir(){
  oParametros = {
    'exec' : 'excluir',
    'db112_sequencial' : inputSequencial.value
  }

  enviar(oParametros).then(response => {
    if(response.status == 1) {
      limpar();
    }
    alert(response.mensagem);
  });
}

function limpar(){
  botaoExcluir.disabled = true;
  inputSequencial.value = '';
  inputDescricaoWorkflow.value = '';
  inputDescricaoProcesso.value = '';
  selectModulo.value = 0;
  selectGrupo.value = 0;
  parent.document.formaba.workflowativ.disabled=true;
}

function js_valordescricao() {
  var
    sDescricao         = inputDescricaoWorkflow.value,
    sDescricaoTipoProc = inputDescricaoProcesso.value;

  if (sDescricaoTipoProc == '') {
    inputDescricaoProcesso.value = inputDescricaoWorkflow.value;
  }
}

function js_validarcampos() {
  var
    sDescricao         = inputDescricaoWorkflow.value,
    sDescricaoTipoProc = inputDescricaoProcesso.value;

  if (sDescricao == '') {
    alert('Campo descrição não informado!');
    return false;
  }

  if (sDescricaoTipoProc == '') {
    alert('Campo descrição do processo de protocolo não informado!');
    return false;
  }

  if(selectModulo.value == 0 || selectModulo.value == ''){
    alert('Campo módulo não informado!');
    return false;
  }

  if(selectGrupo.value == 0 || selectGrupo.value == ''){
    alert('Campo grupo não informado!');
    return false;
  }

  return true;
}

function js_pesquisa() {
  var sUrl = 'func_workflow.php?funcao_js=parent.js_preenchepesquisa|db112_sequencial';
  js_OpenJanelaIframe('CurrentWindow.corpo.iframe_workflow', 'db_iframe_workflow', sUrl, 'Pesquisa', true, '0');
}

function js_preenchepesquisa(chave) {

  db_iframe_workflow.hide();

  oParametros = {
    'exec' : 'buscarDadosWorkflow',
    'db112_sequencial' : chave
  };

  enviar(oParametros).then(response => {
    if(response.status == 1) {
      preencheDadosWorkflow(response.workflow);
    } else {
      alert(response.mensagem);
    }
  });
}

function js_db_libera(){
  parent.document.formaba.workflow.disabled=false;
  parent.document.formaba.workflowativ.disabled=false;
  (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_workflowativ.location.href=`hab1_workflowativ001.php?db112_sequencial=${inputSequencial.value}`;
}

</script>