<?php
/**
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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<html>
<head>
  <title>DBSeller Informática Ltda - Página Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
  <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">

  <style>
    .gridExames {
      width: 1300px;
      margin: auto;
    }
  </style>
</head>
<body class="body-default" onload="$('codigoBarras').focus()">
<div class="container">
  <form>
    <fieldset>
      <legend>Status Exame</legend>
      <table class="form-container">
        <tr>
          <td>
            <label id="labelRequisicao" for="la22_i_codigo">
              <a href="#">Requisição:</a>
            </label>
          </td>
          <td>
            <input id="la22_i_codigo" type="text" value="" class="field-size2" />
            <input id="z01_v_nome" type="text" value="" class="field-size7 readonly" readonly="readonly" />
          </td>
        </tr>

        <tr>
          <td>
            <label for="codigoBarras">Código de Barras:</label>
          </td>
          <td>
            <input id="codigoBarras" type="text" value="" class="field-size-max" maxlength="12" />
          </td>
        </tr>

        <tr>
          <td>
            <label for="material">Material:</label>
          </td>
          <td>
            <select id="material">
              <option value="">Selecione um Material</option>
            </select>
          </td>
        </tr>

        <tr>
          <td>
            <label for="situacaoMaterial">Situação:</label>
          </td>
          <td>
            <input id="situacaoMaterial" type="text" value="" class="field-size-max readonly" readonly="readonly" />
          </td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>

<div id="gridExames" class="gridExames"></div>
</body>
</html>
<script>
  new DBLookUp(
    $('labelRequisicao'),
    $('la22_i_codigo'),
    $('z01_v_nome'),
    {
      sArquivo: 'func_lab_requisicao.php',
      fCallBack: function() {
        buscaMateriais();
      }
    }
  );

  let collectionExames = new Collection().setId('codigo');
  let gridExames = DatagridCollection.create(collectionExames);
  gridExames.configure({order: false, height: 200});
  gridExames.addColumn('descricao', {label: 'Exame', width: '40%'});
  gridExames.addColumn('situacao', {label: 'Situação', width: '20%'});
  gridExames.addColumn('motivo', {label: 'Motivo', width: '40%'});
  gridExames.show($('gridExames'));

  const buscaMateriais = (codigoMaterial = null) => {
    const form = new FormData();
    gridExames.clear();
    $('situacaoMaterial').value = '';

    form.append('acao', 'buscaMateriaisPorRequisicao');
    form.append('codigoRequisicao', $('la22_i_codigo').value);

    HttpClient.post('lab3_statusmaterialexame.RPC.php', {body: form}).then(response => {
      if (response.erro) {
          return alert(response.mensagem);
      }
      setSelectMateriais(response.listaMateriais, codigoMaterial);
    });
  }

  const setSelectMateriais = (listaMateriais, codigoMaterial = null) => {
    const select = $('material');
    var selected;
    select.innerHTML = '<option value="">Selecione um Material</option>';

    listaMateriais.forEach((obj) => {
      selected = '';
      if (codigoMaterial && codigoMaterial == obj.la15_i_codigo) {
          selected = 'selected';
      }
      select.innerHTML += `<option value="${obj.la15_i_codigo}" ${selected}>${obj.la15_c_descr}</option>`;
    });
  }

  const setGridExames = (listaExames) => {
    gridExames.clear();
    var liberado = true;

    for (var i = 0; i < listaExames.length; i++) {
      var motivo = '';
      if(listaExames[i].la21_motivonovacoleta.length > 75){
        motivo = listaExames[i].la21_motivonovacoleta.substr(0, 75);
        motivo += '...'
      }else if(listaExames[i].la21_motivonovacoleta != ''){
        motivo = listaExames[i].la21_motivonovacoleta;
      }
      var obj = {
        codigo: listaExames[i].id,
        situacao: listaExames[i].la21_c_situacao,
        descricao: listaExames[i].la08_c_descr,
        motivo: motivo
      };

      collectionExames.add(obj, 1);     
      
      if (listaExames[i].la21_c_situacao[0] !== '6' && listaExames[i].la21_c_situacao[0] !== '4' && listaExames[i].la21_c_situacao[0] !== '7') {
        
        liberado = false;
      } 
    }

    if (listaExames) {
      $('situacaoMaterial').value = liberado ? 'Liberado' : 'Aguardando';
    } else {
      $('situacaoMaterial').value = '';
    }
    gridExames.reload();

    const trs = gridExames.target.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].children;

    for(var i = 0; i < trs.length; i++){
      if(trs[i].children[1].childNodes[0].data == '90 - Nova Coleta' || trs[i].children[1].childNodes[0].data == '40 - Nova Coleta'){
        //vermelho
        trs[i].setAttribute('style', 'background-color: gold');
      } else if (trs[i].children[1].childNodes[0].data == '70 - Entregue' || trs[i].children[1].childNodes[0].data == '60 - Conferido'){
        //verde
        trs[i].setAttribute('style', 'background-color: #80b554');
      } else if(trs[i].children[1].childNodes[0].data == '45 - Em processamento'){
        trs[i].setAttribute('style', 'background-color: lightblue');
      } else if(trs[i].children[1].childNodes[0].data == '80 - Amostra Rejeitada - Nova Coleta'){
        trs[i].setAttribute('style', 'background-color: lightcoral');
      }
      
      if(trs[i].children[2].childNodes[0].data != ""){
        trs[i].children[2].setAttribute('title', listaExames[i].la21_motivonovacoleta);
      };
    }
  }

  const buscaExames = () => {
    const form = new FormData();

    form.append('acao', 'buscaExamesPorMaterialRequisicao');
    form.append('codigoRequisicao', $('la22_i_codigo').value);
    form.append('codigoMaterial', $('material').value);

    HttpClient.post('lab3_statusmaterialexame.RPC.php', {body: form}).then(response => {
      if (response.erro) {
        return alert(response.mensagem);
      }

      var chave = 0;
      var exames = [];

      for (var i = 0; i < response.listaExames.length; i++) {
          exames.push({
            id: chave,
            la21_c_situacao: response.listaExames[i].la21_c_situacao,
            la08_c_descr: response.listaExames[i].la08_c_descr,
            la21_motivonovacoleta: response.listaExames[i].la21_motivonovacoleta || ''
          });

          chave += 1;
      }
      setGridExames(exames);
    });
  }

  const buscaPorCodigoBarras = () => {
    const campoCodigoBarras = $('codigoBarras');

    if(!js_ValidaCampos(campoCodigoBarras, 1, 'Código de Barras', false, true)) {
      return false;
    }

    if (campoCodigoBarras.value !== '' && campoCodigoBarras.value.length !== 12) {

      alert('Código de Barras inválido.');
      return false;
    }

    if (campoCodigoBarras.value) {
      const form = new FormData();
      const codigoMaterial = parseInt(campoCodigoBarras.value.substring(0, 3), 10);
      const codigoRequisicao = parseInt(campoCodigoBarras.value.substring(3, 12), 10);

      form.append('acao', 'buscaDadosPorCodigoBarras');
      form.append('codigoRequisicao', codigoRequisicao);
      form.append('codigoMaterial', codigoMaterial);

      HttpClient.post('lab3_statusmaterialexame.RPC.php', {body: form}).then(response => {
        if (response.erro) {
          return alert(response.mensagem);
        }

        $('la22_i_codigo').value = codigoRequisicao;
        $('z01_v_nome').value = response.solicitante;

        buscaMateriais(codigoMaterial);

        var requisicaoExames = response.requisicao.requisicaoExames;
        var listaExames = [];
        var chave = 0;
        var materiaisColeta;

        for (var i = 0; i < requisicaoExames.length; i++) {
          materiaisColeta = requisicaoExames[i].lab_exame.materiaisColeta;

          for (var j = 0; j < materiaisColeta.length; j++) {

            if (materiaisColeta[j].la15_i_codigo == codigoMaterial) {
              listaExames.push({
                id: requisicaoExames[i].lab_exame.la08_i_codigo,
                la21_c_situacao: requisicaoExames[i].la21_c_situacao,
                la08_c_descr: requisicaoExames[i].lab_exame.la08_c_descr
              });

              chave += 1;
            }
          }
        }
        setGridExames(listaExames)
      });
    }
  }

  $('material').addEventListener('change', buscaExames);
  $('codigoBarras').addEventListener('blur', buscaPorCodigoBarras);
  $('codigoBarras').addEventListener('focus', (event) => {
    event.srcElement.value = '';
  });
  $('la22_i_codigo').addEventListener('change', (event) => {
      if (event.srcElement.value == '') {
        location.reload(true);
      }
  });

</script>
