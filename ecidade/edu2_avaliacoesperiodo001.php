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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$db_opcao = 1;
$oRotulo  = new rotulocampo();
?>
<html xmlns="http://www.w3.org/1999/html">
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js,
                  prototype.js,
                  strings.js,
                  arrays.js,
                  DBFormCache.js,
                  DBFormSelectCache.js,
                  windowAux.widget.js,
                  datagrid.widget.js,
                  dbtextField.widget.js,
                  dbcomboBox.widget.js");

    db_app::load('widgets/DBToggleList.widget.js');
    db_app::load('classes/educacao/escola/ListaDisciplinas.classe.js');
    db_app::load("estilos.css, grid.style.css, dbVisualizadorImpressaoTexto.style.css");
    ?>
  </head>
  <body class="body-default">
    <div class="container">
      <form name="form1" id='frmAvaliacaoPeriodo' method="post">
        <fieldset>
          <legend>Registro de Avaliações por Período</legend>
          <table class="form-container">
            <tr>
              <td>
                <label for="cboEscola">Escola:</label>
              </td>
              <td nowrap id="ctnCboEscola"></td>
            </tr>
            <tr>
              <td>
                <label for="cboCalendario">Calendário:</label>
              </td>
              <td nowrap id="ctnCboCalendario"></td>
            </tr>
            <tr>
              <td>
                <label for="cboTurma">Turma:</label>
              </td>
              <td nowrap id="ctnCboTurma"></td>
            </tr>
            <tr id="etapaMulti" style="display:none;">
              <td>
                <label for="cboEtapaMulti">Etapas:</label>
              </td>
              <td nowrap id="ctnCboEtapaMulti"></td>
            </tr>
            <tr>
              <td>
                <label for="cboPeriodo">Período:</label>
              </td>
              <td nowrap id="ctnCboPeriodo"></td>
            </tr>
            <tr>
              <td>
                <label for="iNumeroAvaliacoes">Nº de Avaliações:</label>
              </td>
              <td nowrap id="ctnCboNumeroAvaliacoes">
                <?php
                $aAvaliacoes = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6);
                db_select('iNumeroAvaliacoes', $aAvaliacoes, true, $db_opcao);
                ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="trocaTurma">Exibir Trocas de Turma:</label>
              </td>
              <td nowrap id="ctnCboTrocaTurma">
                <?php
                $aTrocaTurma = array(1=>"Não", 2=>"Sim");
                db_select('trocaTurma', $aTrocaTurma, true, $db_opcao);
                ?>
              </td>
            </tr>
            <tr>
              <td>
                <label for="modelo">Modelo:</label>
              </td>
              <td nowrap id="ctnCboNumeroAvaliacoes">
                <select id="modelo" name="modelo" onChange="js_validaModelo()">
                  <option value="1">Modelo 1</option>
                  <option value="2">Modelo 2</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <label for="alunosAtivos">Exibir somente alunos ativos:</label>
              </td>
              <td nowrap id="ctnCboNumeroAvaliacoes">
                <select id="alunosAtivos" name="alunosAtivos">
                  <option value="2">Sim</option>
                  <option value="1">Não</option>
                </select>
              </td>
            </tr>
            <tr id="recuperacao" style="display: none;">
              <td>
                <label for="exibirRecuperacao">Exibir recuperação:</label>
              </td>
              <td nowrap id="ctnCboNumeroAvaliacoes">
                <select id="exibirRecuperacao" name="exibirRecuperacao">
                  <option value="2">Sim</option>
                  <option value="1">Não</option>
                </select>
              </td>
            </tr>

          </table>
            <fieldset id="fieldsetDisciplinas" class='separator'>
                <legend>Disciplinas para impressão</legend>
                <div id='listaDisciplinas' ></div>
            </fieldset>
        </fieldset>
        <input name="btnProcessarRelatorio" id="btnProcessarRelatorio" type="button" value="Processar Relatório">
      </form>
    </div>
  </body>
</html>
<script language="javascript" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
sUrlRPC = 'edu4_escola.RPC.php';

var oCboEscola                  = null;
var oCboCalendario              = null;
var oCboTurma                   = null;
var oCboEtapaMulti              = null;
var oCboPeriodo                 = null;
var oCboDisciplinas             = null;
var oCboDisciplinasSelecionadas = null;
var oDBFormCache                = null;
const codTurma = [];
var armazenaEtapa = {};
var oDisciplina = new DBViewFormularioEducacao.ListaDisciplinas();
oDisciplina.show($('listaDisciplinas'));

function js_init() {

	oCboEscola = new DBComboBox("cboEscola", "oCboEscola", null, "100%");
	oCboEscola.addItem("", "Selecione");
	oCboEscola.addEvent("onChange", "js_pesquisarCalendarios()");
	oCboEscola.show($('ctnCboEscola'));

	oCboCalendario = new DBComboBox("cboCalendario", "oCboCalendario", null, "100%");
	oCboCalendario.addItem("", "Selecione");
	oCboCalendario.addEvent("onChange", "js_pesquisarTurmas()");
	oCboCalendario.show($('ctnCboCalendario'));

	oCboTurma = new DBComboBox("cboTurma", "oCboTurma", null, "100%");
	oCboTurma.addItem("", "Selecione");
	oCboTurma.addEvent("onChange", "js_etapasMulti()");
	oCboTurma.show($('ctnCboTurma'));

	oCboEtapaMulti = new DBComboBox("cboEtapaMulti", "oCboEtapaMulti", null, "100%");
	oCboEtapaMulti.addItem("", "Selecione");
	oCboEtapaMulti.addEvent("onChange", "js_pesquisarPeriodos()");
	oCboEtapaMulti.show($('ctnCboEtapaMulti'));

	oCboPeriodo = new DBComboBox("cboPeriodo", "oCboPeriodo", null, "100%");
	oCboPeriodo.addItem("", "Selecione");
	oCboPeriodo.addEvent("onChange", "js_pesquisarDisciplinas()");
	oCboPeriodo.show($('ctnCboPeriodo'));

	js_validaModelo();
	js_pesquisaEscolas();

	oDBFormCache = new DBFormCache('oDBFormCache', 'edu2_avaliacoesperiodo001.php');
  oDBFormCache.setElements(new Array($('trocaTurma'), $('iNumeroAvaliacoes')));
  oDBFormCache.load();
}

function js_pesquisaEscolas() {

	var oParametro          = {};
	oParametro.exec         = 'getEscola';
	oParametro.filtraModulo = true;
	js_divCarregando("Aguarde, carregando as escolas.", "msgBox");
	new Ajax.Request(
	                 sUrlRPC,
	                 {
	                   method:     'post',
	                   parameters: 'json='+Object.toJSON(oParametro),
	                   onComplete: js_retornaPesquisaEscolas
	                 }
	                );
}

function js_retornaPesquisaEscolas(oResponse) {

	var oRetorno = JSON.parse(oResponse.responseText);
	js_removeObj("msgBox");

	oCboEscola.clearItens();
	oCboEscola.addItem("", "Selecione");
	oCboCalendario.clearItens();
	oCboCalendario.addItem("", "Selecione");
	oCboTurma.clearItens();
	oCboTurma.addItem("", "Selecione");
	oCboPeriodo.clearItens();
    oCboPeriodo.addItem("", "Selecione");
    oDisciplina.clear();

	oRetorno.itens.each(function(oLinha) {
		oCboEscola.addItem(oLinha.codigo_escola, oLinha.nome_escola.urlDecode());
	});

	if (oRetorno.itens.length == 1) {

		oCboEscola.setValue(oRetorno.itens[0].codigo_escola);
		js_pesquisarCalendarios();
	}
}

function js_pesquisarCalendarios() {

	if(oCboEscola.getValue() == '') {

		oCboCalendario.clearItens();
		oCboCalendario.addItem("", "Selecione");
		oCboTurma.clearItens();
		oCboTurma.addItem("", "Selecione");
		oCboPeriodo.clearItens();
		oCboPeriodo.addItem("", "Selecione");
        oDisciplina.clear();
		return false;
	}
  var oParametro    = {};
  oParametro.exec   = 'PesquisaCalendario';
  oParametro.escola = oCboEscola.getValue();

  js_divCarregando("Aguarde, pesquisando calendários.", "msgBox");
  new Ajax.Request(
                   sUrlRPC,
                   {
                     method:     'post',
                     parameters: 'json='+Object.toJSON(oParametro),
                     onComplete: js_retornaPesquisarCalendarios
                   }
                  );
}

function js_retornaPesquisarCalendarios(oResponse) {

	var oRetorno = JSON.parse(oResponse.responseText);
	js_removeObj("msgBox");

	oCboCalendario.clearItens();
	oCboCalendario.addItem("", "Selecione");
	oCboTurma.clearItens();
	oCboTurma.addItem("", "Selecione");
	oCboPeriodo.clearItens();
	oCboPeriodo.addItem("", "Selecione");
    oDisciplina.clear();

	oRetorno.aResult.each(function(oLinha) {
		oCboCalendario.addItem(oLinha.ed52_i_codigo, oLinha.ed52_c_descr.urlDecode());
	});

	if (oRetorno.aResult.length == 1) {
		oCboCalendario.setValue(oRetorno.aResult[0].ed52_i_codigo);
		js_pesquisarTurmas();
	}
}

function js_pesquisarTurmas() {

	if(oCboCalendario.getValue() == '') {

		oCboTurma.clearItens();
		oCboTurma.addItem("", "Selecione");
		oCboPeriodo.clearItens();
		oCboPeriodo.addItem("", "Selecione");
        oDisciplina.clear();
		return false;
	}
    let calendario = document.querySelector('#cboCalendario').value;
    PHPSession.loadData().then(() => {
        HttpClient.get(`${PHPSession.requestApi}/educacao/escola/turmasMulti/${calendario}`).then(response => {
            response.data.map(turma => {
                codTurma.push(turma.ed57_i_codigo.toString());
            })
        });
    });

	var oParametro         = {};
	oParametro.exec        = 'buscaTurmasPorCalendarioEscola';
	oParametro.iEscola     = oCboEscola.getValue();
	oParametro.iCalendario = oCboCalendario.getValue();

	js_divCarregando("Aguarde, carregando as turmas.", "msgBox");
	new Ajax.Request(
	                 'edu4_turmas.RPC.php',
	                 {
	                   method:     'post',
	                   parameters: 'json='+Object.toJSON(oParametro),
	                   onComplete: js_retornaPesquisarTurmas
	                 }
	                );
}

function js_retornaPesquisarTurmas(oResponse) {

	var oRetorno = JSON.parse(oResponse.responseText);
	js_removeObj("msgBox");

	oCboTurma.clearItens();
	oCboTurma.addItem("", "Selecione");
	oCboPeriodo.clearItens();
	oCboPeriodo.addItem("", "Selecione");
  oDisciplina.clear();
  let checkTurma = [];
    oRetorno.aTurmas.forEach(function(oTurma, iSeq) {

        let nome = oTurma.sTurma.urlDecode() + ' - ' + oTurma.sEtapa.urlDecode();
        var cod = oTurma.iTurma.toString();

        etapa = oTurma.iEtapa;
        nomeEtapa = oTurma.sEtapa.urlDecode()

        if(codTurma.includes(cod) && !checkTurma.includes(cod)) {

          nome = oTurma.sTurma.urlDecode();
          oCboTurma.addItem(oTurma.iTurma, nome, null, [{nome: 'etapa', valor: oTurma.iEtapa}]);
          checkTurma.push(cod);
          armazenaEtapa[[cod]] = {...armazenaEtapa[[cod]], [oTurma.iEtapa]: nomeEtapa};
          return;
        }else if(checkTurma.includes(cod)){
          armazenaEtapa[[cod]] = {...armazenaEtapa[[cod]], [oTurma.iEtapa]: nomeEtapa};
          return;
        }

        oCboTurma.addItem(oTurma.iTurma, nome, null, [{nome: 'etapa', valor: oTurma.iEtapa}]);

    });

	if (oRetorno.aTurmas.length == 1) {

		oCboTurma.setValue(oRetorno.aTurmas[0].iTurma);
		js_pesquisarPeriodos();
	}
}

function js_etapasMulti(){

  if (codTurma.includes(oCboTurma.getValue())){
    document.getElementById("etapaMulti").style.display="table-row";
    js_pesquisarPeriodosMulti();
    return;
  }
  document.getElementById("etapaMulti").style.display="none";
  js_pesquisarPeriodos();

}
function js_pesquisarPeriodosMulti() {

  let cod = oCboTurma.getValue();
  let etapaArray = armazenaEtapa[cod];
  let listaEtapas = [];
  let nome = '';

  oCboEtapaMulti.clearItens();
  oCboEtapaMulti.addItem("", "Selecione");
  oCboPeriodo.clearItens();
	oCboPeriodo.addItem("", "Selecione");
  oDisciplina.clear();

  for(var key in etapaArray ){
    nome = etapaArray[key];
    oCboEtapaMulti.addItem(cod, nome, null, [{nome: 'etapa', valor: [key]}]);
    listaEtapas.push(key);
  }
  nome = 'TODAS ETAPAS';
  oCboEtapaMulti.addItem(cod, nome, null, [{nome: 'etapa', valor: listaEtapas.join(',')}]);
  return;
}

function js_pesquisarPeriodos() {

	if(oCboTurma.getValue() == '') {

		oCboPeriodo.clearItens();
		oCboPeriodo.addItem("", "Selecione");
        oDisciplina.clear();
		return false;
	}

	var oParametro    = {};
	oParametro.exec   = 'getPeriodosAvaliacaoPorTurma';
	oParametro.iTurma = oCboTurma.getValue();

	js_divCarregando("Aguarde, carregando os períodos da turma.", "msgBox");
	new Ajax.Request(
	                 sUrlRPC,
	                 {
	                   method:     'post',
	                   parameters: 'json='+Object.toJSON(oParametro),
	                   onComplete: js_retornaPesquisarPeriodos
	                 }
	                );
}

function js_retornaPesquisarPeriodos(oResponse) {

	var oRetorno = JSON.parse(oResponse.responseText);
    js_removeObj("msgBox");

    oCboPeriodo.clearItens();
	oCboPeriodo.addItem("", "Selecione");
    oDisciplina.clear();

    oRetorno.aPeriodos.each(function(oLinha) {
        oCboPeriodo.addItem(oLinha.codigo_periodo, oLinha.descricao_periodo.urlDecode());
    });

  if(oRetorno.aPeriodos.length == 1) {

    oCboPeriodo.setValue(oRetorno.aPeriodos[0].codigo_periodo);
    js_pesquisarDisciplinas();
  }
}

function js_pesquisarDisciplinas() {

	if(oCboPeriodo.getValue() == '') {
        oDisciplina.clear();
		return false;
	}

    oDisciplina.clear();
    var codigoEt = oCboTurma.getAttributeOptionSelected('etapa');


    if (codTurma.includes(oCboTurma.getValue())){
      codigoEt = oCboEtapaMulti.getAttributeOptionSelected('etapa');
      var listaEtapaMult = codigoEt.split(',');
      if(listaEtapaMult.length  > 1){
        codigoEt = listaEtapaMult[0];
      }
    }

    oDisciplina.getDisciplinas(oCboTurma.getValue(), codigoEt, false);
}

function js_verificaCampos() {

	var iEscola                  = oCboEscola.getValue();
	var iCalendario              = oCboCalendario.getValue();
	var iTurma                   = oCboTurma.getValue();
	var iPeriodo                 = oCboPeriodo.getValue();
    var aDisciplinaSelecionadas = oDisciplina.getSelecionados();

  if(iEscola == '') {

  	alert('Nenhuma escola foi selecionada');
    return false;
  }

  if(iCalendario == '') {

  	alert('Nenhum calendário foi selecionado');
    return false;
  }

  if(iTurma == '') {

  	alert('Nenhuma turma foi selecionada');
    return false;
  }

  if(iPeriodo == '') {

  	alert('Nenhum período foi selecionado');
    return false;
  }

  if (aDisciplinaSelecionadas.length == 0) {

    alert('Nenhuma Disciplina selecionada');
    return false;
  }
}

$('btnProcessarRelatorio').observe("click", function() {
  let codEtapa = oCboTurma.getAttributeOptionSelected('etapa');
  // Validação se tem mais de uma etapa
  if (codTurma.includes(oCboTurma.getValue())){
    codEtapa = oCboEtapaMulti.getAttributeOptionSelected('etapa');
    var aEtapa = codEtapa.split(',');
  }



    oDBFormCache.save();
	if (js_verificaCampos() == false) {
		return false;
	}

    var aDisciplinaSelecionadas = oDisciplina.getSelecionados();

	var aDisciplinas             = [];

    aDisciplinaSelecionadas.each(function(oDisciplina, id) {
        aDisciplinas.push(oDisciplina.iRegencia);
    });

	var sLocation = "edu2_avaliacoesperiodo002.php?";

	if ($F('modelo') == 2) {
	  sLocation = "edu2_avaliacoesperiodo003.php?";
    if (codTurma.includes(oCboTurma.getValue())){
      if(codEtapa.split(',').length > 1){
        sLocation = "edu2_avaliacoesperiodo004.php?";
      }
    }
	}

	sLocation += "iEscola="+oCboEscola.getValue();
	sLocation += "&iCalendario="+oCboCalendario.getValue();
	sLocation += "&iTurma="+oCboTurma.getValue();
	sLocation += "&iEtapa="+codEtapa;
	sLocation += "&iPeriodo="+oCboPeriodo.getValue();
	sLocation += "&iAvaliacoes="+$F('iNumeroAvaliacoes');
	sLocation += "&aDisciplinas="+aDisciplinas;
	sLocation += "&trocaTurma="+$F('trocaTurma');
	sLocation += "&iAlunosAtivos="+$F('alunosAtivos');
	sLocation += "&iExibirRecuperacao="+$F('exibirRecuperacao');

	var jan = window.open(sLocation,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  jan.moveTo(0,0);
});

js_init();

function js_validaModelo() {

  switch ($F('modelo')) {

    case '1':

      $('recuperacao').style.display = 'none';
      break;
    case '2':

      $('recuperacao').style.display = 'table-row';
      break;
  }
}
</script>

<?php
    db_menu();
?>
