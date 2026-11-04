<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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

require_once("libs/db_stdlibwebseller.php");
require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");
require_once("libs/db_sessoes.php");
require_once("libs/db_usuariosonline.php");
require_once("libs/db_utils.php");
require_once("libs/db_app.utils.php");
require_once("dbforms/db_funcoes.php");
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
//  db_app::load("classes/educacao/escola/ListaTurmaParecer.classe.js");
  ?>
  <script type="text/javascript" ></script>
</head>
<body class="body-default">
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
        <legend>Alunos Avalidos Por Parecer</legend>
        <table class="form-container">

          <tr>
            <td nowrap="nowrap" class='bold'>Ano Letivo:</td>
            <td nowrap="nowrap" id='listaCalendario'></td>
          </tr>
<!--          <tr>-->
<!--            <td nowrap="nowrap" class='bold'>Turma:</td>-->
<!--            <td nowrap="nowrap" id='listaTurmas'></td>-->
<!--          </tr>-->
        </table>
      </fieldset>
      <input type="button"  id='imprimir' value='Imprimir' name='imprimir' />
    </form>
  </div>
</body>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
<script>

var oEscola     = new DBViewFormularioEducacao.ListaEscola();
var oCalendario = new DBViewFormularioEducacao.ListaCalendario();
// var oTurma      = new DBViewFormularioEducacao.ListaTurma();

oCalendario.setEscola(<?=db_getsession("DB_coddepto");?>);
oCalendario.getCalendarios();


var fFunctionLoadCalendario = function(oCalendario) {

  $('imprimir').setAttribute("disabled", "disabled");

};

var fFunctionChangeCalendario = function() {

  var oEscolaSelecionada     = <?=db_getsession("DB_coddepto");?>;
  var mCalendarioSelecionado = oCalendario.getSelecionados();
  var aListaCalendarios      = new Array();

  if (oCalendario.lAgruparPorAno) {

    if (mCalendarioSelecionado.length == 0) {

//        oTurma.limpar();
      $('imprimir').setAttribute("disabled", "disabled");
      return false;
    }

    for (var i = 0; i < mCalendarioSelecionado.length; i++) {
      aListaCalendarios.push(mCalendarioSelecionado[i].iCalendario)
    };

  } else {

    if (mCalendarioSelecionado.iCalendario == '') {

//      oTurma.limpar();
      return false;
    }

    aListaCalendarios.push(mCalendarioSelecionado.iCalendario);

  }

  //oTurma.setEscola(<?//=db_getsession("DB_coddepto")?>//);
  // oTurma.setCalendario(aListaCalendarios.implode(", "));
//  oTurma.getTurmas();
    $('imprimir').removeAttribute("disabled");

};

/**
 * callBack para Turma
 */
var fCallBackChangeTurma = function () {

  // var oTurmaSelecionada = oTurma.getSelecionados();
//  $('imprimir').setAttribute("disabled", "disabled");
//   if (oTurmaSelecionada.codigo_turma != '') {
//    $('imprimir').removeAttribute("disabled");
//   }

};


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
// oTurma.setCallbackOnChange(fCallBackChangeTurma);
// oTurma.setCallBackLoad(fCallBackChangeTurma);
// oTurma.habilitarOpcaoTodas(true);
//oTurma.show($('listaTurmas'));

/**
 * Função para imprimir os dados do formulário1
 * @return
 */
$('imprimir').observe("click", function () {

  var aCalendariosSelecionados = oCalendario.getSelecionados();
//  var oTurmaSelecionada        = oTurma.getSelecionados();
//
  var aCalendarios = new Array();
  var iAno         = aCalendariosSelecionados[0].iAno;
  aCalendariosSelecionados.each( function (oCalendario) {
    aCalendarios.push(oCalendario.iCalendario);
  });

  var sUrl  = "edu2_alunoavaliadoparecer002.php";
      sUrl += "?iEscola="+<?=db_getsession("DB_coddepto")?>; //+oEscolaSelecionada.codigo_escola;
      sUrl += "&iAno="+iAno;
//      sUrl += "&iTurma="+oTurmaSelecionada.codigo_turma;


  jan = window.open(sUrl,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  jan.moveTo(0,0);
});

</script>
</html>
