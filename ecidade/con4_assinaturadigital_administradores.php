<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2021 DBSeller Servicos de Informatica
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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

$clrotulo = new rotulocampo;
$clrotulo->label("db67_codigo");
$clrotulo->label("db67_nome");
$clrotulo->label("db67_tipo");
$clrotulo->label("db67_cpf_cnpj");
$clrotulo->label("db67_id_usuario");
$clrotulo->label("db67_permissao");

$tipos = [
  'PF' => 'Pessoa Física',
  'PJ' => 'Pessoa Jurídica'
];

$permissoes = [
  'S' => 'ASSINANTE',
  'A' => 'ADMIN'
];

$db_opcao = 1;

?>
<html>
<head>
  <title>DBSeller Informática Ltda</title>
  <meta http-equiv="Expires" CONTENT="0">
  <?php
    /**
     * Default
     */
    $aLibs   = array("scripts.js");
    $aLibs[] = "prototype.js";
    $aLibs[] = "AjaxRequest.js";
    $aLibs[] = "strings.js";
    $aLibs[] = "classes/http/http.js";
    $aLibs[] = "estilos.css";

    /**
     * Datagrid
     */
    $aLibs[] = "datagrid.widget.js";
    $aLibs[] = "grid.style.css";

    /**
     * Collections
     */
    $aLibs[] = "Collection.widget.js";
    $aLibs[] = "DatagridCollection.widget.js";
    $aLibs[] = "FormCollection.widget.js";

    /**
     * DBLookUp
     */
    $aLibs[] = "DBLookUp.widget.js";

    /**
     * DBHint
     */
    $aLibs[] = "widgets/DBHint.widget.js";
    $aLibs[] = "widgets/datagrid/plugins/DBHint.plugin.js";
    
    db_app::load(implode(",", $aLibs));
  ?>
</head>
<body>

<form class="container">
  <fieldset>
    <legend>Usuários</legend>
    <table>
      <tr style="display: none;">
      <!-- <tr> -->
        <td>
          <label for="db67_codigo"><?= $Ldb67_codigo ?></label>
        </td>
        <td>
          <input id="db67_codigo" name="db67_codigo" class="field-size3 readOnly" disabled readonly />
        </td>
      </tr>
      <tr>
        <td>
          <label for="db67_id_usuario">
            <a id="ancora_usuario" href="#"><?= $Ldb67_id_usuario ?></a>
          </label>
        </td>
        <td>
          <input type="text" id="db67_id_usuario" name="db67_id_usuario" class="field-size3" lang="id_usuario" />
        </td>
        <td colspan="2">
          <input type="text" id="db67_nome" name="db67_nome" class="field-size7 readonly" disabled="true" lang="nome" />
        </td>
      </tr>
      <tr>
        <td>
          <label for="db67_cpf_cnpj"><?= $Ldb67_cpf_cnpj ?></label>
        </td>
        <td colspan="3">
          <?php db_input('db67_cpf_cnpj', 20, '', true, 'text', 3, "", "", "", "field-size6", 20) ?>
        </td>
      </tr>
      <tr>
        <td>
          <label for="db67_tipo"><?= $Ldb67_tipo ?></label>
        </td>
        <td>
          <?php db_select('db67_tipo', $tipos, true, $db_opcao, 'class="field-size3"') ?>
        </td>
        <td>
          <label for="db67_permissao"><?= $Ldb67_permissao ?></label>
        </td>
        <td>
          <?php db_select('db67_permissao', $permissoes, true, $db_opcao, 'class="field-size5"') ?>
        </td>
      </tr>
    </table>
  </fieldset>
  <input type="button" value="Salvar"  id="salvar"           />
  <input type="button" value="Excluir" id="excluir" disabled />
  <input type="button" value="Novo"    id="cancelar"         />
</form>

<div>
  <fieldset>
    <legend>Permissões</legend>
    <div id="gridUsuariosPermitidos"></div>
  </fieldset>
</div>

<?php db_menu(); ?>

<script type="text/javascript">

String.prototype.formatterCPFCNPJ = function () {
  const cpf_cnpj = this.replace(/\D/g, '');
  let
    replacement = "$1.$2.$3-$4",
    regex = /(\d{3})(\d{3})(\d{3})(\d+)/
  ;
  
  if (cpf_cnpj.length == 14) {
    replacement = "$1.$2.$3/$4-$5";
    regex = /(\d{2})(\d{3})(\d{3})(\d{4})(\d+)/;
  }
  
  return cpf_cnpj.replace(new RegExp(regex, 'g'), replacement);
};

const URL = "<?= ECIDADE_REQUEST_PATH;?>v4/api/configuracao/assinantes";
const collectionUsuarios = Collection.create().setId("db67_codigo");

/**
 * Trata o valor que entrará na coleção
 */
collectionUsuarios.setEvent("onBeforeCreate", function(itemCollection) {
  itemCollection.db67_codigo = itemCollection.db67_codigo || '---';
  return true;
});

const lookupUsuario = new DBLookUp($('ancora_usuario'), $('db67_id_usuario'), $('db67_nome'), {
  arquivo : "func_db_usuarios.php",
  label   : "Pesquisa de Usuários",
  aParametrosAdicionais : [ "retorna_cpf_cnpj=true" ],
  aCamposAdicionais : [ 'z01_cgccpf' ],
});
lookupUsuario.setCallBack('onChange', function (erro, attributos) { 
  const f = (nome, cpf_cnpj) => $('db67_cpf_cnpj').value = !!cpf_cnpj ? cpf_cnpj.formatterCPFCNPJ() : '';
  f.apply(this, attributos);
});
lookupUsuario.setCallBack('onClick',  function (attributos) {
  const f = (id_usuario, nome, cpf_cnpj) => $('db67_cpf_cnpj').value = !!cpf_cnpj ? cpf_cnpj.formatterCPFCNPJ() : '';
  f.apply(this, attributos);
});

$('db67_id_usuario').observe('blur', ev => {
  const value = ev.target.value;

  if (!value) {
    $('db67_cpf_cnpj').value = '';
  }
})

const gridUsuPer = new DatagridCollection(collectionUsuarios);
      gridUsuPer.configure({
        order : false,
        action: {
          label: "Ações",
          width: "30px",
          align: "center"
        }
      });

      gridUsuPer.addColumn("db67_codigo", {
        label : 'Código',
        align : 'center',
        width : '30px'
      })

      gridUsuPer.addColumn("db67_id_usuario")
                .configure('label', 'ID - Usuário')
                .configure('align', 'center')
                .configure('width', '40px');

      gridUsuPer.addColumn("db67_nome")
                .configure('label','Nome')
                .configure('width', '200px');

      gridUsuPer.addColumn("db67_cpf_cnpj")
                .configure('label', 'CPF/CNPJ')
                .configure('align', 'center')
                .configure('width', '60px')
                .transform(cpf_cnpj => cpf_cnpj.formatterCPFCNPJ());

      gridUsuPer.addColumn("db67_tipo")
                .configure('label', 'Tipo')
                .configure('align', 'center')
                .configure('width', '70px')
                .transform(tipo => tipo += ((tipo == 'PF') ? ' - Pessoa Física' : ' - Pessoa Jurídica'));

      gridUsuPer.addColumn("db67_permissao")
                .configure('label', 'Permissão')
                .configure('align', 'center')
                .configure('width', '50px');

const formUsuPer = new FormCollection(gridUsuPer, document.forms[0]);
      formUsuPer.makeBehavior($('salvar'),   'save',   salvar);
      formUsuPer.makeBehavior($('excluir'),  'delete', removerPermissao);
      formUsuPer.makeBehavior($('cancelar'), 'cancel', limpar);
      formUsuPer.onAfterSelectRow(function (acao, itemCollection) {
        lookupUsuario.habilitar();
        $('db67_tipo').setAttribute('disabled', true);
        $('db67_permissao').removeAttribute('disabled');

        if(acao == 'E') {
          lookupUsuario.desabilitar();
          $('db67_permissao').setAttribute('disabled', true);
        }

        $('db67_codigo').value     = itemCollection.db67_codigo;
        $('db67_id_usuario').value = itemCollection.db67_id_usuario;
        $('db67_nome').value       = itemCollection.db67_nome;
        $('db67_cpf_cnpj').value   = itemCollection.db67_cpf_cnpj;
        $('db67_tipo').value       = itemCollection.db67_tipo;
        $('db67_permissao').value  = itemCollection.db67_permissao == 'ADMIN' ? 'A' : 'S';
      });

      gridUsuPer.show($('gridUsuariosPermitidos'));

carregarGrid();
$('cancelar').observe('click', limpar);

function carregarGrid() {
  HttpClient.get(URL, {
    reportMessage : 'Buscando permissões de usuários'
  }).then(res => {
    if (!!res && !!res.message) {
      alert(res.message);
    }

    if (!!res && !!res.erro) return;

    const usuarios = res.data;

    gridUsuPer.clear();
    usuarios.map(usr => {
      regex       = /^(\d{3})(\d{3})(\d{3})(\d+$)/;
      replacement = "$1.$2.$3-$4";
      
      if (usr.db67_tipo == 'PJ') {
        regex       = /^(\d{2})(\d{3})(\d{3})(\d{4})(\d+$)/;
        replacement = "$1.$2.$3/$4-$5";
      }
      
      usr.db67_cpf_cnpj = usr.db67_cpf_cnpj.replace(new RegExp(regex, 'g'), replacement);

      return usr;
    }).forEach(usuario => collectionUsuarios.add(usuario));

    gridUsuPer.reload();
  }).catch(e => e.message ? alert(e.message) : console.error(e));
}

function salvar(db67_cpf_cnpj, db67_id_usuario, db67_permissao, db67_tipo) {
  const camposErro = [];
  const formData = new FormData();
  const form = document.querySelector('form');
  form.serialize().split('&').forEach(f => {
    const field = f.split('=').shift();
    const value = f.split('=').pop();

    if (!value && field != 'db67_codigo') {
      const fieldElement = document.getElementById(field);

      if (!fieldElement) {
        return;
      }

      const labelField = fieldElement.labels[0].innerText.trim().replace(':', '');
      camposErro.push(labelField);
    }
  });

  if (camposErro.length > 0) {
    alert("Existem campos não preenchidos, verifique:\n- "+ camposErro.join("\n- "));
    return false;
  }

  formData.append('id_usuario', $F('db67_id_usuario'));
  formData.append('nome',       $F('db67_nome'));
  formData.append('cpf_cnpj',   $F('db67_cpf_cnpj').replace(/\D/g, ''));
  formData.append('tipo',       $F('db67_tipo'));
  formData.append('permissao',  $F('db67_permissao') == 'S' ? 'ASSINANTE' : 'ADMIN');

  if (!!$F('db67_codigo')) {
    formData.append('codigo', $F('db67_codigo'));
  }

  const
    url = URL,
    options = {
      reportMessage : "Salvando permissões do usuário",
      body : formData
    }
  ;

  if (!$F('db67_codigo')) {
    novaPermissao(url, options).then(() => limpar());
    return false;
  }

  options.reportMessage = "Atualizando permissões do usuário";
  atualizarPermissao(url, options).then(() => limpar());
  
  return false;
}

function atualizarGrid(res) {
  return new Promise((resolve, reject) => {
    if (!!res && !!res.message) {
      alert(res.message);
    }

    if (!!res && !!res.erro) return reject(res.erro);

    const usuario = res.data;

    collectionUsuarios.add(usuario);
    gridUsuPer.reload();
    $('db67_codigo').value = '';

    resolve(res);
  });
}

function novaPermissao(url, options) {
  return new Promise((resolve, reject) => {
    HttpClient.post(url, options)
              .then(res => atualizarGrid(res))
              .then(() => resolve())
                .catch(e => {
                  if (e.message) {
                    alert(e.message)
                  } else {
                    console.error(e)
                  }
                  reject();
                });
  });
}

function atualizarPermissao(url, options) {
  return new Promise((resolve, reject) => {
    HttpClient.post(url, options)
              .then(res => atualizarGrid(res))
              .then(() => resolve())
              .catch(e => {
                if (e.message) {
                  alert(e.message)
                } else {
                  console.error(e)
                }
                reject();
              });
  });
}

function removerPermissao(itemCollection) {
  const id = itemCollection.db67_codigo;

  HttpClient.delete(`${URL}/${id}`, {
    reportMessage : "Excluindo permissões do usuário"
  }).then(res => {
    if (res.message) {
      alert(res.message);
    }

    if (res.erro) return;

    collectionUsuarios.remove(id);
    gridUsuPer.reload();
    limpar();
    $('db67_codigo').value = '';
  }).catch(e => e.message ? alert(e.message) : console.error(e));
}

function limpar() {
  formUsuPer.clearForm();
  lookupUsuario.habilitar();
  $('db67_tipo').removeAttribute('disabled');
  $('db67_permissao').removeAttribute('disabled');
}
</script>
</body>
</html>
