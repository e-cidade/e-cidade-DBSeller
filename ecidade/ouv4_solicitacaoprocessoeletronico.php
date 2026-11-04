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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<?php
  db_app::load("estilos.css, grid.style.css");
?>
<style>
td {
  white-space: nowrap
}

.fieldset-data {
  text-align: left;
  padding-left: 20px;
}

.label-data{
  padding-left: 10px;
  padding-right: 10px;
}

.button-container{
  margin-top: 10px;
  margin-bottom: 10px;
}

</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top" bgcolor="#CCCCCC">
    <center>
      <form id='form_avaliacao_processo'>
        <fieldset>
          <legend><b>Avaliacão</b></legend>
          <fieldset class='fieldset-data'>
            <legend><b>Filtros</b></legend>
            <label><b>Período: </b></label>
            <?php db_inputdata('dataInicial', null, null, null, true, 'text', 1);?>
            <label class="label-data"><b> à </b></label>
            <?php db_inputdata('dataFinal', null, null, null, true, 'text', 1);?>
          </fieldset>
          <div class='button-container'>
            <input type="button" value="Pesquisar" id="btnPesquisar"/>
          </div>
          <fieldset>
            <legend><b>Solicitações</b></legend>
            <div id="container_processos" style="margin-top: 5px; width: 800px;"></div>
          </fieldset>
        </fieldset>
      </form>
    </center>
  </td>
  </tr>
</table>
<script>
  const
    urlRpc = 'ouv4_solicitacaoprocessoeletronico.RPC.php',
    oProcessosCollection = new Collection().setId('processo'),
    btnPesquisar   = $('btnPesquisar'),
    oDataInicial   = $('dataInicial'),
    oDataFinal     = $('dataFinal'),
    oGridProcessos = DatagridCollection.create(oProcessosCollection).configure("order", false);

  let iframe = null;

  initGrid();

  btnPesquisar.addEventListener('click', event => {
    atualizaGrid();
  });

  function atualizaGrid(){
    getProcessos().then(response => {
      oProcessosCollection.clear();
      for (var oProcesso of response) {
        oProcessosCollection.add({
          sequencial            : oProcesso.sequencial,
          solicitante           : oProcesso.solicitante,
          tipoProcesso          : oProcesso.tipo_processo,
          tipoProcessoDescricao : oProcesso.tipo_processo_descricao,
          processo              : oProcesso.processo,
          data                  : oProcesso.data
        });
      }
      oGridProcessos.reload();
    });
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

  function getProcessos(){
    var
      oParametros = {
        'exec'       : 'buscarProcessosAlvara',
        'dataInicio' : oDataInicial.value,
        'dataFim'    : oDataFinal.value
      },
      formData = createFormData(oParametros);

    return HttpClient.post(urlRpc, {body: formData}).then(response => {

      if(response.processos.length == 0) {
        alert('Nenhuma solicitação encontrada para este departamento');
      }
      return response.processos;
    });
  }

  function initGrid(){

    oGridProcessos.addColumn("sequencial",            {label : "ID",            "width" :  "40px"}).setOption("align","center");
    oGridProcessos
      .addColumn("processo",{label : "Atendimento",   "width" : "100px"})
      .setOption("align","center")
      .transformCallback = function (processo, itemCollection) {
        return `<a class='codigo_processo' onclick="detalhamento('${processo}', '${itemCollection.tipoProcesso}', 'true')">${processo}</a>`;
      };
    oGridProcessos.addColumn("tipoProcessoDescricao", {label : "Tipo",          "width" : "200px"}).setOption("align","center");
    oGridProcessos.addColumn("solicitante",           {label : "Solicitante",   "width" : "280px"}).setOption("align","center");
    oGridProcessos.addColumn("data",                  {label : "Data",          "width" :  "80px"}).setOption("align","center");

    oGridProcessos.addAction("Visualizar", null, function(event, oItem) {
      detalhamento(oItem.processo, oItem.tipoProcesso, false);
    });

    oGridProcessos.show($("container_processos"));

  }

  function fechaIframe(){
    iframe.hide();
  }

  function detalhamento(processo, tipoProcesso, view){
    let reg       = new RegExp(/(.*)\/(\d{4})/);
    let anoNumero = reg.exec(processo);

    if(anoNumero == null) {
      alert("Não foi possível encontrar o número da solicitação.");
      return;
    }

    let params;
        params  = 'processo=';
        params += anoNumero[1];
        params += '&';
        params += 'ano=';
        params += anoNumero[2];
        params += '&';
        params += 'tipoProcesso=';
        params += tipoProcesso;
        params += '&';
        params += 'escondeBotoes=';
        params += view;

      if(!iframe){
        iframe = js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_liberacao_alvara', `ouv4_conferenciasolicitacaoprocessoeletronico.php?${params}`, 'Solicitação de Álvará', true, 0, 0);
      } else {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_liberacao_alvara', `ouv4_conferenciasolicitacaoprocessoeletronico.php?${params}`, 'Solicitação de Álvará', true, 0, 0);
      }
  }
</script>
</body>
</html>
