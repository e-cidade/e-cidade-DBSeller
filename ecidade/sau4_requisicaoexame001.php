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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
$oGet = db_utils::postMemory( $_GET );

$oRotulo = new rotulocampo;
$oRotulo->label("z01_v_nome");
$oRotulo->label("sd24_i_codigo");
$oRotulo->label("sd03_i_codigo");

$iProntuario = $oGet->iProntuario;
$numCgs = $oGet->numCgs;
$sPaciente = $oGet->sNomePaciente;
$iProfissional = $oGet->iProfissional;
$sProfissional = $oGet->sNomeProfissional;

?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <link rel="stylesheet" type="text/css" href="estilos.css" />
  <link rel="stylesheet" type="text/css" href="estilos/grid.style.css" />
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
</head>
<body class='body-default'>

  <div class="container" style="width:800px;">
    <form>

      <fieldset>
        <div id="status-microarea" class="alert-danger" style="text-align: center;" role="alert" hidden>
          Paciente sem cadastro em uma microárea!
        </div>
        <legend>Solicitação e Avaliação de Exames</legend>
        <table>
          <tr>
              <td colspan="2">
                  <div style="padding-left: 395px" id="linhaDaIdadeCompleta"></div>
              </td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="bold"><?=$Lsd24_i_codigo?></td>
            <td nowrap="nowrap" >
              <?php
                db_input("iRequisicao", 10, "", true, "hidden", 3 );
                db_input("iProntuario", 10, "", true, "text", 3 );
                db_input('numCgs', 10, '', true, 'hidden', 3);
                db_input("sPaciente",   60, "", true, "text", 3 );
              ?>
            </td>
          </tr>

          <tr>
            <td nowrap="nowrap" class="bold"><?=$Lsd03_i_codigo?></td>
            <td nowrap="nowrap" >
              <?php
                db_input("iProfissional", 10, "", true, "text", 3 );
                db_input("sProfissional", 60, "", true, "text", 3 );
              ?>
            </td>

        </table>

        <fieldset style="margin-top:10px">
        <legend>Exames</legend>
        <table>

        <tr>
          <td nowrap >
            <?php db_ancora ( "Exames:", "js_pesquisala19_i_exame(true);", 1 );?>
          </td>
          <td>
            <?php
              db_input ( 'iCodigoExame', 10, "", true, 'hidden', 1, "onchange='js_pesquisala19_i_exame(false);'" );
              db_input ( 'iSigla', 10, "", true, 'text', 3, "" );
              db_input ( 'sNomeExame', 62, "", true, 'text', 1, '' );

            ?>
          </td>
        </tr>


        <tr>
          <td><strong>Solicitado:</strong></td>
          <td>
          <?php
              $aOpcoes = array( "0"=>"Não", "1"=>"Sim" );
              db_select("lSolicitado", $aOpcoes, true, 1)
            ?>
          </td>
        </tr>


        <tr>
          <td><strong>Avaliado:</strong></td>
          <td>
            <?php
              $aOpcoes = array( "0"=>"Não", "1"=>"Sim" );
              db_select("lAvaliado", $aOpcoes, true, 1)
            ?>
          </td>
        </tr>

        </table>

        <input type="button" name='adicionar_exame'   id='adicionar_exame' onclick="js_adicionarExame();" value="Adicionar" />

        <fieldset class='separator'>
          <legend>Observação do Exame</legend>
          <textarea  id='observacao' rows="4" cols="40"  style="min-height:47px !important;" maxlength="550"> </textarea>
        </fieldset>
      </fieldset>

      <div id="gridEexamesNovos" style="margin-top:10px;"></div>


      <input type="button" name='imprimir' id='imprimirRequisicao' value="Imprimir" disabled="disabled" />
    </form>
  </div>


</body>
<script rel="script" type="text/javascript" src="scripts/classes/saude/ValidaCgs.js"></script>
<script type="text/javascript">

const MSG_SAU4_REQUISICAOEXAME = 'saude.ambulatorial.sau4_requisicaoexame.';
const divAlert = document.getElementById('status-microarea');
const inputCgs = {
  id: document.getElementById('numCgs'),
  nome: document.getElementById('sPaciente')
}
const validaCgs = new ValidaCgs(inputCgs);

var oGet         = js_urlToObject();
var iTotalExames = 0;
var aLinhasGrid = [];

var linhas = [];

var oGridExamesNovo         = new DBGrid("GridExamesNovo");
oGridExamesNovo.nameInstance = 'oGridExamesNovo';
//oGridExamesNovo.setCheckbox(0);
oGridExamesNovo.setHeader(['Cód', 'Sigla','Exame', 'Solicitado', 'Avaliado', 'Ação']);
oGridExamesNovo.setCellWidth( ["5%", '20%', '50%', '10%', '10%', '5%' ] );
oGridExamesNovo.setCellAlign( [ 'right', 'right', 'left', 'center', 'center' ] );
oGridExamesNovo.aHeaders[0].lDisplayed = false;
oGridExamesNovo.setHeight(300);
oGridExamesNovo.show($('gridEexamesNovos'));
oGridExamesNovo.clearAll(true);

window.onload = () => {
  validaCgs.cadastroMicroarea(inputCgs, divAlert);
};

function js_pesquisala19_i_exame(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_lab_exame','func_lab_examesigla.php?funcao_js=parent.js_mostralab_exame1|la08_i_codigo|la08_c_descr|la08_c_sigla','Pesquisa',true);
  }else{

     if($("iCodigoExame").value != ''){
        js_OpenJanelaIframe('','db_iframe_lab_exame','func_lab_examesigla.php?pesquisa_chave='+$F("iCodigoExame")+'&funcao_js=parent.js_mostralab_exame','Pesquisa', false);
     }else{
       $("iCodigoExame").value = '';
     }
  }
}
function js_mostralab_exame(chave,erro,chave2, sigla){


  $("sNomeExame").value = chave;
  $("iSigla").value = sigla;
  if(erro==true){
    $("iCodigoExame").focus();
    $("iCodigoExame").value= "";
    $("sNomeExame").value = 'Exame Não encontrado';
    $("iSigla").value = "";
  }
}
function js_mostralab_exame1(chave1,chave2,chave3){
  $("iCodigoExame").value = chave1;
  $("sNomeExame").value = chave2;
  $("iSigla").value = chave3;
  db_iframe_lab_exame.hide();
}


function js_adicionarExame(){

  iExame = $F("iCodigoExame");
  lAvaliado = $F("lAvaliado");
  lSolicitado = $F("lSolicitado");
  sDescricao = $F("sNomeExame");


  if (iExame == "" ) {
    alert("Selecione um Exame.");
    return false;
  }
  if ( lAvaliado == "0" && lSolicitado == "0" ) {
    alert("Condição Avaliado e/ou Solicitado é obrigatório. Selecionar SIM para, pelo menos, uma opção");
    return false;
  }


  var oParametros         = new Object();
	  oParametros.sExecucao = 'incluirExameNaRequisicao';
      oParametros.exame = iExame;
      oParametros.prontuario = $F("iProntuario");
      oParametros.avaliado = lAvaliado;
      oParametros.solicitado = lSolicitado;
      oParametros.medico = $F('iProfissional');
      oParametros.sObservacao = encodeURIComponent(tagString( $F('observacao') ));

      new AjaxRequest("sau4_requisicaoexameprontuario.RPC.php", oParametros, js_retornoAdicionarExame).execute();
}

function js_retornoAdicionarExame(oRetorno)
{

    if (oRetorno.erro) {
        alert(oRetorno.sMensagem.urlDecode());
    }
    $("iCodigoExame").value = "";
    $("iSigla").value = "";
    $("sNomeExame").value = "";
    $("lSolicitado").options[0].selected = "0";
    $("lAvaliado").options[0].selected = "0";
    js_getExameRequisicao();


}


function js_getExameRequisicao()
{
  var oParametros = new Object();
	  oParametros.sExecucao = 'getExameNaRequisicao';
      oParametros.iProntuario = $F("iProntuario");
      oParametros.medico = $F('iProfissional');
      new AjaxRequest("sau4_requisicaoexameprontuario.RPC.php", oParametros, js_retornoGetExameRequisicao).execute();
}

function js_retornoGetExameRequisicao(oRetorno)
{

  $("imprimirRequisicao").disabled = "disabled";

  if (oRetorno.aListaExames.length > 0 ) {
    $("imprimirRequisicao").disabled = "";
  }
    oGridExamesNovo.clearAll(true);

    $("observacao").value = oRetorno.sObservacao.urlDecode();
    oRetorno.aListaExames.each(function(oDado){

     var oBotao   = document.createElement('input');
         oBotao.type  = 'button';
         oBotao.value = 'E';
         oBotao.id    = 'removerExame_' + oDado.iExameRequisicao ;
         oBotao.setAttribute('onClick', "js_removeExame("+oDado.iExameRequisicao+")");

     var oBotaoAlterar   = document.createElement('input');
         oBotaoAlterar.type  = 'button';
         oBotaoAlterar.value = 'A';
         oBotaoAlterar.id    = 'removerExame_' + oDado.iExameRequisicao ;
         oBotaoAlterar.setAttribute('onClick', "js_alterarExame("+oDado.iExameRequisicao+")");

     var aRow    = new Array();
         aRow[0] = oDado.iExame;
         aRow[1] = oDado.sigla;
         aRow[2] = oDado.sExame.urlDecode();
         aRow[3] = oDado.solicitado.urlDecode();
         aRow[4] = oDado.avaliado.urlDecode();
         aRow[5] = oBotaoAlterar.outerHTML + ' ' + oBotao.outerHTML;

      oGridExamesNovo.addRow(aRow);
   });

  oGridExamesNovo.renderRows();
}


function js_alterarExame(iCodigo){

    let oParametros = new Object();
        oParametros.sExecucao = 'getExameNaRequisicao';
        oParametros.iProntuario = $F("iProntuario");
        oParametros.exame = iCodigo;
    new AjaxRequest("sau4_requisicaoexameprontuario.RPC.php", oParametros, js_retornoDadosAlterar).execute();

}
function js_retornoDadosAlterar(oRetorno){

    oRetorno.aListaExames.each(function(oDado){

      $("iCodigoExame").value = oDado.iExame;
      $("sNomeExame").value = oDado.sExame.urlDecode();
      $("iSigla").value = oDado.sigla;
      $("lSolicitado").options[oDado.lSolicitado].selected = oDado.lSolicitado;
      $("lAvaliado").options[oDado.lAvaliado].selected = oDado.lAvaliado;
        console.log(oDado);
    });
}



function js_removeExame( iCodigo ){

    let oParametros = new Object();
	    oParametros.sExecucao = 'excluirExameNaRequisicao';
        oParametros.iProntuario = $F("iProntuario");
        oParametros.medico = $F('iProfissional');
        oParametros.exame = iCodigo;
    new AjaxRequest("sau4_requisicaoexameprontuario.RPC.php", oParametros, js_retornoRemoveExame).execute();

}

function js_retornoRemoveExame(oRetorno)
{
  if (oRetorno.erro) {
    alert(oRetorno.sMensagem.urlDecode());
  }
  js_getExameRequisicao();
}




$('imprimirRequisicao').observe('click', function () {

    var sUrl = "sau2_requisicaoexame004.php?iProntuario=" + $F('iProntuario');
    var oJanela = window.open(sUrl,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    oJanela.moveTo(0,0);
});


js_getExameRequisicao();


// Autocomplete do CGS
oAutoComplete = new dbAutoComplete($('sNomeExame'), 'sau4_pesquisaexame.RPC.php?tipo=1');
oAutoComplete.setTxtFieldId($('iCodigoExame'));
oAutoComplete.setHeightList(300);
oAutoComplete.show();
oAutoComplete.setCallBackFunction(function(id,label,data) {

  $("iCodigoExame").value = data.cod;
  $("iSigla").value = data.sigla;
  $("sNomeExame").value = data.label.urlDecode();;

});

function retornaIdadePaciente(oRetorno, lErro) {
    let idadeCompleta = oRetorno.oCgs.sIdadeCompleta.urlDecode();
    $('linhaDaIdadeCompleta').innerHTML  = `<p style="display: inline-block">Idade: <span style="color: red;">${idadeCompleta}</span></p>`;
}

function existeCgsUnd() {
    let cgsUnd = document.getElementById('numCgs').value;

    if (cgsUnd !== '') {
        let diretrizes    = {'sExecucao': 'buscarDadosCgs','iCgs': cgsUnd}
        let oAjaxRequest  = new AjaxRequest('sau4_cgs.RPC.php', diretrizes, retornaIdadePaciente);
        oAjaxRequest.execute();
    }
}
existeCgsUnd();

</script>
</html>
