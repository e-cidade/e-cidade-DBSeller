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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <?php
  db_app::load("scripts.js, prototype.js, strings.js");
  db_app::load("estilos.css");
  db_app::load("classes/educacao/escola/ListaEscola.classe.js");
  db_app::load("classes/educacao/escola/ListaCalendario.classe.js");
  db_app::load("classes/educacao/escola/ListaEtapa.classe.js");
  db_app::load("scripts.js, prototype.js, strings.js, arrays.js, windowAux.widget.js, datagrid.widget.js,
                dbtextField.widget.js, dbcomboBox.widget.js");
  db_app::load("estilos.css, grid.style.css,dbVisualizadorImpressaoTexto.style.css");
  
  ?>
  <script type="text/javascript" ></script>
</head>
<body bgcolor="#cccccc" style='margin-top: 30px'>
  <?php
    /**
     * Validamos se estamos no módulo escola
     */
    if (db_getsession("DB_modulo") == 1100747) {
    	MsgAviso(db_getsession("DB_coddepto"),"escola");
    }
  ?>
  <div class='container'>
    <form id='formPadrao' action="">
      <fieldset>
        <legend>Relatório de Alunos com Bolsa Família</legend>
        <table class="form-container">
          <tr>
            <td nowrap="nowrap" class='bold'>Escola:</td>
            <td nowrap="nowrap" id='listaEscola'></td>
          </tr>
          <tr>
            <td nowrap="nowrap" class='bold'>Ano Letivo:</td>
            <td nowrap="nowrap" id='listaCalendario'></td>
          </tr>
          <tr>
            <td  nowrap="nowrap" title="" >
              <b>Período: </b>
            </td>
            <td nowrap="nowrap" id="ctnCboPeriodo">
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap" class='bold'>Etapas:</td>
            <td nowrap="nowrap" id='listaEtapas'></td>
          </tr>
          <tr>
            <td></td>
            <td>
              <label>
                <input type="checkbox" value="t" id="lFrequencia" name="lFrequencia">
               Somente alunos com frequência inferior a 85% (06 a 15 anos) e 75% (16 e 17 anos)
              </label>
            </td>
          </tr>
        </table>
      </fieldset>
      <input type="button" disabled='disabled' id='imprimir' value='Imprimir' name='imprimir' />
    </form>
  </div>
</body>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
<script>

var oEscola     = new DBViewFormularioEducacao.ListaEscola();
var oCalendario = new DBViewFormularioEducacao.ListaCalendario();
var oEtapas     = new DBViewFormularioEducacao.ListaEtapa();
var oCboPeriodo            = new DBComboBox("cboPeriodo", "oCboPeriodo", null, "100%");
oCboPeriodo.addItem("", "Selecione um Período");
oCboPeriodo.show($('ctnCboPeriodo'));

function js_pesquisarPeriodos() {

var escolaSelecionada = oEscola.getSelecionados();
var calendarios = oCalendario.getSelecionados();

if(calendarios[0].iCalendario == '') {
  oCboPeriodo.clearItens();
  oCboPeriodo.clearItens();
  return false;
}

var oParametro         = new Object();
oParametro.exec        = 'buscaPeriodosAvaliacaoEscola';
oParametro.iEscola     = escolaSelecionada.codigo_escola;
oParametro.iCalendario = calendarios[0].iCalendario;

js_divCarregando('Aguarde, carregando os períodos.', 'msgBox');
var oAjax = new Ajax.Request('edu_educacaobase.RPC.php',
                             {
                               method:     'post',
                               parameters: 'json='+Object.toJSON(oParametro),
                               onComplete: js_retornaPesquisarPeriodos
                             }
                            );
}

function js_retornaPesquisarPeriodos(oAjax) {
var oRetorno = JSON.parse(oAjax.responseText);
js_removeObj('msgBox');
oCboPeriodo.clearItens();
oCboPeriodo.addItem("", "Selecione");

oRetorno.dados.each(function(oPeriodo, iSeq) {
   oCboPeriodo.addItem(oPeriodo.codigo_periodo, oPeriodo.descricao_periodo.urlDecode());
});

if( oRetorno.dados.length == 1 ) {

  oCboPeriodo.setValue( oRetorno.dados[0].codigo_periodo );
}
}

var fFuncaoLoadEscola = function() {

  if (this.oCboEscola.options.length > 2) {
    this.oCboEscola.value = 0;
  }

  var oEscolaSelecionada = oEscola.getSelecionados();

  if (oEscolaSelecionada.codigo_escola != '') {

    oCalendario.setEscola(oEscolaSelecionada.codigo_escola);
    oCalendario.getCalendarios();
  }

};

var fFuncaoChangeEscola = function () {

  var oEscolaSelecionada = oEscola.getSelecionados();
  if (oEscolaSelecionada.codigo_escola == '') {

    oCalendario.limpar();
    oEtapas.limpar();
    $('imprimir').setAttribute("disabled", "disabled");
  } else {

    oCalendario.setEscola(oEscolaSelecionada.codigo_escola);
    oCalendario.getCalendarios();
    oEtapas.limpar();
   }
};

var fFunctionLoadCalendario = function() {

  $('imprimir').setAttribute("disabled", "disabled");

  if( oCalendario.aCalendarios.length == 1 ) {

    oCboPeriodo.clearItens();
    oCboPeriodo.addItem("", "Selecione");
    js_pesquisarPeriodos();
    oEtapas.limpar();
    oEtapas.pesquisaEtapas();
  }
};

var fFunctionChangeCalendario = function() {

  var oEscolaSelecionada     = oEscola.getSelecionados();
  var mCalendarioSelecionado = oCalendario.getSelecionados();
  var aListaCalendarios      = new Array();

  if (oCalendario.lAgruparPorAno) {

    if (mCalendarioSelecionado.length == 0) {

      oCboPeriodo.clearItens();
      oCboPeriodo.addItem("", "Selecione");
      oEtapas.limpar();
      $('imprimir').setAttribute("disabled", "disabled");
      return false;
    }

    for (var i = 0; i < mCalendarioSelecionado.length; i++) {
      aListaCalendarios.push(mCalendarioSelecionado[i].iCalendario)
    };
  } else {

    if (mCalendarioSelecionado.iCalendario == '') {

      oCboPeriodo.clearItens();
      oCboPeriodo.addItem("", "Selecione");
      oEtapas.limpar();
      return false;
    }

    aListaCalendarios.push(mCalendarioSelecionado.iCalendario);
  }

  oEtapas.setEscola(oEscolaSelecionada.codigo_escola);
  oEtapas.setCalendario(aListaCalendarios.implode(", "));
  oEtapas.pesquisaEtapas();
  js_pesquisarPeriodos();

};

/**
 * callBack para etapa
 */
var fCallBackChangeEtapa = function () {

  var oEtapaSelecionada = oEtapas.getSelecionados();
  $('imprimir').setAttribute("disabled", "disabled");
  if (oEtapaSelecionada.codigo_etapa != '') {
    $('imprimir').removeAttribute("disabled");
  }

};


/**
 * seta os callback da escola
 */
oEscola.setCallBackLoad(fFuncaoLoadEscola);       // Opcional
oEscola.setCallbackOnChange(fFuncaoChangeEscola); // Opcional

oEscola.habilitarOpcaoTodas(true);                // Opcional
oEscola.show($('listaEscola'));


/**
 * seta os callback do calendário
 */
oCalendario.setCallBackLoad(fFunctionLoadCalendario);
oCalendario.setOnChangeCallBack(fFunctionChangeCalendario);

oCalendario.agruparPorAno(true);
oCalendario.show($('listaCalendario'));

/**
 * Seta callback na etapa
 */
oEtapas.setCallbackOnChange(fCallBackChangeEtapa);
oEtapas.setCallBackLoad(fCallBackChangeEtapa);

oEtapas.habilitarOpcaoTodas(true);
oEtapas.show($('listaEtapas'));

/**
 * Função para imprimir os dados do formulário1
 * @return
 */
$('imprimir').observe("click", function () {

  var oEscolaSelecionada       = oEscola.getSelecionados();
  var aCalendariosSelecionados = oCalendario.getSelecionados();
  var oEtapaSelecionada        = oEtapas.getSelecionados();

  var aCalendarios = new Array();
  var iAno         = aCalendariosSelecionados[0].iAno;
  aCalendariosSelecionados.each( function (oCalendario) {
    aCalendarios.push(oCalendario.iCalendario);
  });

  if(oCboPeriodo.getValue() == null || oCboPeriodo.getValue() == '') {
    alert('Nenhum período selecionado');
    return false;
  }

  var sUrl  = "edu2_alunobolsa002.php";
      sUrl += "?iEscola="+oEscolaSelecionada.codigo_escola;
      sUrl += "&aCalendarios="+aCalendarios;
      sUrl += "&iAno="+iAno;
      sUrl += "&iPeriodo="+oCboPeriodo.getValue();
      sUrl += "&oPeriodo="+oCboPeriodo.getDescricao();
      sUrl += "&iEtapa="+oEtapaSelecionada.codigo_etapa;
      sUrl += "&lFrequencia="+$('lFrequencia').checked;

  jan = window.open(sUrl,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  jan.moveTo(0,0);
});

</script>
</html>
