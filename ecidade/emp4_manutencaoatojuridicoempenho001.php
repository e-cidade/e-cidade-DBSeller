<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidadedbseller.com.br
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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$oGet                       = db_utils::postMemory($_GET);
$oRotuloEmpEmpenho          = new rotulo("empempenho");
$oRotuloEmpenhoOutrosDados  = new rotulo("empempenhooutrosdados");
$oRotuloCgm  = new rotulo("cgm");

$oRotuloEmpEmpenho->label();
$oRotuloEmpenhoOutrosDados->label();
$oRotuloCgm->label();
$oGet->db_opcao = 1;

db_app::load('estilos.css,, ');

?>

<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
      <link href="estilos.css" rel="stylesheet" type="text/css">
      <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
      <script type="text/javascript" src="scripts/scripts.js"></script>
      <script type="text/javascript" src="scripts/prototype.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbtextField.widget.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbcomboBox.widget.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBHint.widget.js"></script>
      <script type="text/javascript" src="scripts/classes/http/http.js"></script>
  </head>

  <body>

    <form class="container" id="form1" name="form1">

      <fieldset style="width: 600px;">
        <legend>Manutenção de dados do ato jurídico do empenho</legend>
        <table class="form-container">
          <!-- Inventário / inventario / t75-->
          <tr>
            <td width="30%" nowrap title="Numcgm">
              <?php
                db_ancora($Lz01_numcgm, "js_pesquisa_cgm(true);", 1);
                ?>
            </td>
            <td>
              <?php
                db_input("z01_numcgm", 10, $Iz01_numcgm, true, "text", 4, "onchange='js_pesquisa_cgm(false);'");
                db_input("z01_nome2", 30, "", true, "text", 3);
                ?>
            </td>
          </tr>
          <tr>
            <td title="<?=$Te60_emiss?>">
              <?php db_ancora($Le60_emiss, "", 3);?>
            </td>
            <td>
              <?php
                $e60_emiss_dia = null;
                $e60_emiss_mes = null;
                $e60_emiss_ano = null;
                db_inputdata('e60_emiss1', $e60_emiss_dia, $e60_emiss_mes, $e60_emiss_ano, true, 'text', 1, "");
                db_inputdata('e60_emiss2', $e60_emiss_dia, $e60_emiss_mes, $e60_emiss_ano, true, 'text', 1, "");
                ?>
            </td>
          </tr>
          <tr>
            <td title="<?=$Te60_emiss?>">
              <?php db_ancora("Intervalo de Empenhos:", "", 3);?>
            </td>
            <td>
              <?php
                db_input("intervaloEmpDe", 10, "", true, "text", 1);
                ?>
                <b>até</b>
                <?php
                db_input("intervaloEmpAte", 10, "", true, "text", 1);
                ?>
            </td>
          </tr>
        </table>
      </fieldset>

      <input name="exibir" id = "pesquisa" type="button" onclick='js_exibirEmpenhos();'  value="Pesquisa">
      <input name="limpa" type="button" onclick='js_limpa();'  value="Limpar campos">
    </form>
<?php
db_menu();
?>

<script>

var sUrlRpc = "emp4_manutencaoatojuridicoempenho.RPC.php";

function js_abre(){

   obj = document.form1;
   if (    (obj.e60_emiss1_dia.value !='')
        && (obj.e60_emiss2_dia.value !='')
        && (obj.e60_emiss1_mes.value !='')
        && (obj.e60_emiss2_mes.value !='')
        && (obj.e60_emiss1_ano.value !='')
        && (obj.e60_emiss1_ano.value !='')) {
    dt1 = obj.e60_emiss1_ano.value+'-'+obj.e60_emiss1_mes.value+'-'+obj.e60_emiss1_dia.value ;
    dt2 = obj.e60_emiss2_ano.value+'-'+obj.e60_emiss2_mes.value+'-'+obj.e60_emiss2_dia.value ;
   } else {
      dt1='';
      dt2='';
   }

  var intervaloEmpenhoDe = document.form1.intervaloEmpDe.value > 0 ? document.form1.intervaloEmpDe.value : 0;
  var intervaloEmpenhoAte = document.form1.intervaloEmpAte.value > 0 ? document.form1.intervaloEmpAte.value : 0;
  js_OpenJanelaIframe(
    'CurrentWindow.corpo',
    'db_iframe_empconsulta002',
    'emp1_empconsulta002.php?z01_numcgm='+document.form1.z01_numcgm.value+
    '&dt1='+dt1+
    '&dt2='+dt2+
    '&intervaloEmpDe='+intervaloEmpenhoDe+
    '&intervaloEmpAte='+intervaloEmpenhoAte+
    '&funcao_js=parent.js_consulta002',
    'Pesquisa',
    true
  );
}

function js_limpa(){
   location.href='emp4_manutencaoatoJuridicoempenho001.php';
}

function js_exibirEmpenhos() {


  if ($F("z01_numcgm") == ""
      && $F("e60_emiss1") == ""
      && $F("e60_emiss2") == ""
      && $F("intervaloEmpDe") == ""
      && $F("intervaloEmpAte") == "") {
    alert("Ao menos um dos filtros deve ser informado");
    return false;
  }

  if($F("e60_emiss1") != "" && $F("e60_emiss2") != ""){
    if(js_comparadata($F("e60_emiss1"), $F("e60_emiss2"), ">")){
      alert("Data final maior que data inicial")
      return false;
    }
  }
  if(($F("e60_emiss1") != "" && $F("e60_emiss2") == "") || ($F("e60_emiss1") == "" && $F("e60_emiss2") != "")){
      alert("As duas datas devem estar preenchidas")
      return false;
  }

  if($F("intervaloEmpDe") != "" && $F("intervaloEmpAte") != "") {
      if ($F("intervaloEmpDe") > $F("intervaloEmpAte")) {
          $("intervaloEmpAte").value = "";
          $("intervaloEmpDe").value = "";
          alert("Intervalo de empenho final menor que intervalo inicial")
          return false;
      }
  }
    if(($F("intervaloEmpDe") != "" && $F("intervaloEmpAte") == "") ||
        ($F("intervaloEmpDe") == "" && $F("intervaloEmpAte") != "")){
        alert("Os dois campos do intervalo de empenho devem ser preenchidos")
        return false;
    }

  const formData = new FormData();
  formData.append('numeroCgm', $F("z01_numcgm"));
  formData.append('dataInicial', $F("e60_emiss1"));
  formData.append('dataFinal', $F("e60_emiss2"));
  formData.append('empenhoInicial', $F("intervaloEmpDe"));
  formData.append('empenhoFinal', $F("intervaloEmpAte"));
  formData.append('acao','getEmpenhoFiltroManutencao');


  HttpClient.post(sUrlRpc,{body:formData}).then(response => {
      if(response.erro){
          alert(response.mensagem);
          return;
      }
      if(response.empenhos){
          js_montaWindowGridItens(response.empenhos)
      }
  });
}

function js_montaWindowGridItens (empenhos) {

  if (empenhos.length == 0) {
    alert("Nenhum empenho encontrado" ,"msgbox");
    return false;
  }

  var iHeight   = document.body.clientHeight-100;
  var iWidth    = document.body.clientWidth;
  var iWidthContainer = (iWidth);
  console.log(iWidthContainer);
  oWindowAux    = new windowAux('oWindowAux', 'Empenhos', iWidth, iHeight);
  var sContent  = "<div style='width: "+iWidthContainer+"px;' id='cntGrid'></div>";
      sContent += "<p align='center'><input type='button' value='Fechar' onclick='js_fecharWindow();' /></p>";
  oWindowAux.setContent(sContent);
  var aHeader     = new Array();
      aHeader[0]  = "Cod. Empenho ";
      aHeader[1]  = "Seq. Empenho";
      aHeader[2]  = "Credor";
      aHeader[3]  = "Emissão";
      aHeader[4]  = "Valor";
      aHeader[5]  = "Tipo Ato Jurídico";
      aHeader[6]  = "Tipo Instrumento";
      aHeader[7]  = "Nº Ato Jurídico";
      aHeader[8]  = "Cod. UG Ato Jurídico";
      aHeader[9]  = "Nº instrumento Prévio";
      aHeader[10]  = "Cod. UG instr. Prévio";


  var aCellWidth     = new Array();
      aCellWidth[0]  = "6%";
      aCellWidth[1]  = "4%";
      aCellWidth[2]  = "22%";
      aCellWidth[3]  = "6%";
      aCellWidth[4]  = "8%";
      aCellWidth[5]  = "6%";
      aCellWidth[6]  = "10%";
      aCellWidth[7]  = "8%";
      aCellWidth[8]  = "10%";
      aCellWidth[9]  = "12%";
      aCellWidth[10] = "12%";


  var aCellAlign     = new Array();
      aCellAlign[0]  = "center";
      aCellAlign[1]  = "center";
      aCellAlign[2]  = "center";
      aCellAlign[3]  = "center";
      aCellAlign[4]  = "center";
      aCellAlign[5]  = "center";
      aCellAlign[6]  = "center";
      aCellAlign[7]  = "center";
      aCellAlign[8]  = "center";
      aCellAlign[9]  = "center";
      aCellAlign[10]  = "center";


  oGridEmpenhos = new DBGrid('cntGrid');
  oGridEmpenhos.nameInstance = 'oGridBens';
  oGridEmpenhos.allowSelectColumns(true);
  oGridEmpenhos.setHeader(aHeader);
  oGridEmpenhos.setCellWidth(aCellWidth);
  oGridEmpenhos.setCellAlign(aCellAlign);
  oGridEmpenhos.setHeight(300);
  oGridEmpenhos.show($('cntGrid'));

  oWindowAux.show();
  oWindowAux.setShutDownFunction(function(){
    js_fecharWindow();
  });

  js_preencheGrid(empenhos);

}

function js_salvaDadosLinhaSelecionada(oRow) {

  let dados = {};
  dados.ato_juridico = oRow.aCells[7].getValue();
  dados.ug_ato_juridico = oRow.aCells[8].getValue();
  dados.instrumento_previo = oRow.aCells[9].getValue();
  dados.ug_instrumento_previo = oRow.aCells[10].getValue();

  const formData = new FormData();
  formData.append('empenho',oRow.aCells[1].getValue());
  formData.append('dados',js_objectToJson(dados));
  formData.append('acao',"salvaOutrosDadosEmpenho")          ;
  HttpClient.post(sUrlRpc,{body :formData}).then(response =>{
      if (response.erro) {
          alert(response.mensagem)
      }
  })
}


function js_preencheGrid(aEmpenho) {

  js_divCarregando("Carregando dados...", "oDivDados");

  oGridEmpenhos.clearAll(true);
  aItensGrid = {};

  aEmpenho.each(function (oItem, iIndice) {

     /* INPUT atoJuridico  */
     var atoJuridico = window["fieldAtoJuridico" + oItem.e60_numemp] = new DBTextField(
        "fieldAtoJuridico"+oItem.e60_numemp,
        "fieldAtoJuridico"+oItem.e60_numemp,
        oItem.ato_juridico,
        10
    );
     atoJuridico.addEvent("onKeyPress", "return js_mask(event,\"0-9|,|-\")");
     atoJuridico.addStyle('width', '100%');
     atoJuridico.addStyle('height', '100%');
     atoJuridico.addStyle('text-align', 'center');
     atoJuridico.addStyle('border', '1px solid black');
     atoJuridico.addEvent("onFocus", "js_liberaDigitacao(this);");
     atoJuridico.addEvent("onBlur", "js_bloqueiaDigitacao(this);js_atualizaValor(this, event);");
     atoJuridico.addEvent(
        "onChange",
        ";js_valorAtualizado(this,"+oItem.e60_numemp+","+iIndice+"); js_atualizaValor(this, event);"
     );
     atoJuridico.addEvent("onKeyUp",';js_ValidaValor(this, event);');
     atoJuridico.setReadOnly(false);

     /* INPUT unidadeGestora  */
     var unidadeGestora = window["fieldUnidadeGestora" + oItem.e60_numemp] = new DBTextField(
        "fieldUnidadeGestora"+oItem.e60_numemp,
        "fieldUnidadeGestora"+oItem.e60_numemp,
        oItem.ug_ato_juridico,
        10
    );
     unidadeGestora.addEvent("onKeyPress", "return js_mask(event,\"0-9|,|-\")");
     unidadeGestora.addStyle('width', '100%');
     unidadeGestora.addStyle('height', '100%');
     unidadeGestora.addStyle('text-align', 'center');
     unidadeGestora.addStyle('border', '1px solid black');
     unidadeGestora.addEvent("onFocus", "js_liberaDigitacao(this);");
     unidadeGestora.addEvent("onBlur", "js_bloqueiaDigitacao(this);js_atualizaValor(this, event);");
     unidadeGestora.addEvent(
        "onChange",
        ";js_valorAtualizado(this,"+oItem.e60_numemp+","+iIndice+"); js_atualizaValor(this, event);"
     );
     unidadeGestora.addEvent("onKeyUp",';js_ValidaValor(this, event);');
     unidadeGestora.setReadOnly(false);


      /* INPUT instrumentoPrevio  */
    var instrumentoPrevio = window["fieldinstrumentoPrevio" + oItem.e60_numemp] = new DBTextField(
        "fieldInstrumentoPrevio"+oItem.e60_numemp,
        "fieldInstrumentoPrevio"+oItem.e60_numemp,
        oItem.instrumento_previo,
        10
    );
     instrumentoPrevio.addEvent("onKeyPress", "return js_mask(event,\"0-9|,|-\")");
     instrumentoPrevio.addStyle('width', '100%');
     instrumentoPrevio.addStyle('height', '100%');
     instrumentoPrevio.addStyle('text-align', 'center');
     instrumentoPrevio.addStyle('border', '1px solid black');
     instrumentoPrevio.addEvent("onFocus", "js_liberaDigitacao(this);");
     instrumentoPrevio.addEvent("onBlur", "js_bloqueiaDigitacao(this);js_atualizaValor(this, event);");
     instrumentoPrevio.addEvent(
        "onChange",
        ";js_valorAtualizado(this,"+oItem.e60_numemp+","+iIndice+"); js_atualizaValor(this, event);"
     );
     instrumentoPrevio.addEvent("onKeyUp",';js_ValidaValor(this, event);');
     instrumentoPrevio.setReadOnly(false);



      /* INPUT codigoUgInstrumentoPrevio  */
    var codigoUgInstrumentoPrevio = window["fieldcodigoUgInstrumentoPrevio" + oItem.e60_numemp] = new DBTextField(
        "fieldcodigoUgInstrumentoPrevio"+oItem.e60_numemp,
        "fieldcodigoUgInstrumentoPrevio"+oItem.e60_numemp,
        oItem.ug_instrumento_previo,
        10
    );

     codigoUgInstrumentoPrevio.addEvent("onKeyPress", "return js_mask(event,\"0-9|,|-\")");
     codigoUgInstrumentoPrevio.addStyle('width', '100%');
     codigoUgInstrumentoPrevio.addStyle('height', '100%');
     codigoUgInstrumentoPrevio.addStyle('text-align', 'center');
     codigoUgInstrumentoPrevio.addStyle('border', '1px solid black');
     codigoUgInstrumentoPrevio.addEvent("onFocus", "js_liberaDigitacao(this);");
     codigoUgInstrumentoPrevio.addEvent("onBlur", "js_bloqueiaDigitacao(this);js_atualizaValor(this, event);");
     codigoUgInstrumentoPrevio.addEvent(
        "onChange",
        ";js_valorAtualizado(this,"+oItem.e60_numemp+","+iIndice+"); js_atualizaValor(this, event);"
     );
     codigoUgInstrumentoPrevio.addEvent("onKeyUp",';js_ValidaValor(this, event);');
     codigoUgInstrumentoPrevio.setReadOnly(false);

    // criado array com os objetos
    aItensGrid[iIndice] = {
        atoJuridico  : atoJuridico,
        unidadeGestora  : unidadeGestora,
        instrumentoPrevio : instrumentoPrevio,
        codigoUgInstrumentoPrevio : codigoUgInstrumentoPrevio
    };

    var aLinha     = new Array();
        aLinha[0]  = oItem.e60_codemp.urlDecode() + "/" + oItem.e60_anousu.urlDecode();
        aLinha[1]  = oItem.e60_numemp.urlDecode();
        aLinha[2]  = oItem.z01_nome.urlDecode();
        aLinha[3]  = js_formatar(oItem.e60_emiss.urlDecode(), 'd');
        aLinha[4]  = js_formatar(oItem.e60_vlremp, 'f');
        aLinha[5]  = oItem.e165_descricao;
        aLinha[6]  = oItem.tipo_instrumento;
        aLinha[7]  = '';
        aLinha[8]  = '';
        aLinha[9]  = '';
        aLinha[10]  = '';

    oGridEmpenhos.addRow(aLinha, false, true);

  });

  oGridEmpenhos.renderRows();

  for (var iIndice = 0; iIndice < oGridEmpenhos.aRows.length; iIndice++) {
    var oItem = aItensGrid[iIndice],
        oRow  = oGridEmpenhos.aRows[iIndice],
        fieldatoJuridico = document.getElementById(oRow.aCells[7].sId);
        fieldUnidadeGestora = document.getElementById(oRow.aCells[8].sId);
        fieldInstrumentoPrevio = document.getElementById(oRow.aCells[9].sId);
        fieldcodigoUgInstrumentoPrevio = document.getElementById(oRow.aCells[10].sId);
        oItem.atoJuridico.show(fieldatoJuridico);
        oItem.unidadeGestora.show(fieldUnidadeGestora);
        oItem.instrumentoPrevio.show(fieldInstrumentoPrevio);
        oItem.codigoUgInstrumentoPrevio.show(fieldcodigoUgInstrumentoPrevio);

  }

  js_removeObj("oDivDados");
}

/**
 * Atualiza o campo C?DIGO AGRUPAMENTO
 */
function js_valorAtualizado(oInputValorAtualizado, iCodigoBem, iCodigoLinha) {

  js_salvaDadosLinhaSelecionada(oGridEmpenhos.aRows[iCodigoLinha]);

}

function js_bloqueiaDigitacao(oObject) {

  oObject.readOnly         = true;
  oObject.style.border     ='1px solid black';
  oObject.style.fontWeight = "normal";
  oObject.value            = oObject.value;
}

function js_liberaDigitacao (object) {

  nValorObjeto            = js_strToFloat(object.value).valueOf();
  object.value            = nValorObjeto;
  object.style.border     = '1px solid black';
  object.readOnly         = false;

  object.select();

  nValorAntigo = object.value;
}


function js_atualizaValor(object, event) {


  var teclaPressionada = event.which;

  if (teclaPressionada == 27) {
    object.value = nValorAntigo;
  }
}

function js_ValidaValor(obj, event) {

if ( js_countOccurs(obj.value, '.') > 1 ) {

  obj.value = js_getInputValue(obj.name);
  obj.focus();
  return false;
}
}

function js_fecharWindow() {
  oWindowAux.destroy();
}

function js_pesquisa_cgm(mostra){
  if(mostra==true){
    js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_cgm',
        'func_cgm_empenho.php?funcao_js=parent.js_mostracgm1|e60_numcgm|z01_nome',
        'Pesquisa',
        true
    );
  }else{
     if(document.form1.z01_numcgm.value != ''){
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_cgm',
            'func_cgm_empenho.php?pesquisa_chave='+document.form1.z01_numcgm.value+'&funcao_js=parent.js_mostracgm',
            'Pesquisa',
            false
        );
     }else{
       document.form1.z01_nome2.value = '';
     }
  }
}

function js_mostracgm(chave,erro){
  document.form1.z01_nome2.value = chave;
  if(erro==true){
    document.form1.z01_nome2.value = '';
    document.form1.z01_numcgm.focus();
  }
}

function js_mostracgm1(chave1,chave2){
   document.form1.z01_numcgm.value = chave1;
   document.form1.z01_nome2.value = chave2;
   db_iframe_cgm.hide();
}

</script>

