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

$clsolicita->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("descrdepto");
$clrotulo->label("pc50_descr");
$clrotulo->label("pc12_vlrap");
$clrotulo->label("pc12_tipo");
$clrotulo->label("o74_sequencial");
$clrotulo->label("o74_descricao");

if (!empty($_SESSION['oSolicita'])) {
    unset($_SESSION['oSolicita']);
}
?>
<form name="form1" method="post" action="">
<center>
  <table>
    <tr>
      <td>
        <fieldset>
          <legend>
            <b>Dados da Abertura</b>
          </legend>
          <table>
            <tr>
              <td nowrap title="Código da Abertura">
                 <b>Código da Abertura:</b>
              </td>
              <td>
                <?php
                db_input('pc10_numero',10,$Ipc10_numero,true,'text',3)
                ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Tpc10_data?>">
                <b>Vigência para Estimativa:</b>
              </td>
              <td>
                <?php
                $recebedata = db_getsession("DB_datausu");
                $recebedata = date("Y-m-d",$recebedata);
                if(isset($pc10_data) && trim($pc10_data) != ""){
                  $recebedata = $pc10_data;
                }
                $arr_data =explode("-",$recebedata);
                @$pc10_datadia = $arr_data[2];
                @$pc10_datames = $arr_data[1];
                @$pc10_dataano = $arr_data[0];
                db_inputdata('pc54_datainicio',null,null,null,true,'text',1);
                echo "&nbsp;<b>a</b>&nbsp;";
                db_inputdata('pc54_datatermino',null,null,null,true,'text',1);
                ?>
              </td>
            <tr>
              <td nowrap title="<?=@$Tpc10_resumo?>">
                <b>Resumo:</b>
              </td>
              <td>
              <?php
               @$pc10_resumo = stripslashes($pc10_resumo);
               db_textarea("pc10_resumo",10,120,"",true,"text",$db_opcao,"","","",735);
              ?>
              </td>
            </tr>
            <tr>
              <td>
                 &nbsp;
              </td>
              <td>

                <input type="checkbox" id='pc54_liberado'>
                <label for="pc54_liberado"><b>Disponibilizar para Estimativa</b></label>

              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center;">
        <input type='button' value='Salvar' id='btnSalvar'>
        <input type='button' value='Imprimir' id='btnImprimir'>
        <?php
          if ($lBtnShowBtnConsulta) {
           echo "<input type='button' value='Pesquisar' id='btnConsultar'>";
          }
        ?>
      </td>
    </tr>
  </table>
</center>
</form>
<script>
var sUrlRC = 'com4_solicitacaoCompras.RPC.php';
const iframeItens = parent.document.querySelector('[framename=iframe_itens]');
iframeItens.preparado = false;
iframeItens.onclick = function (e) {
     if (e.target.preparado === false) {
            return alert('Informe os dados da abertura do Registro de Preço.');
     }

    parent.mo_camada('itens');
}

function dataInicioPosteriorFinal(inicio, final) {
    const dataInicio = inicio.split('/').reverse().join('-');
    const dataFinal = final.split('/').reverse().join('-');

    return dataInicio > dataFinal;
}

function js_salvarAbertura() {
   const dataInicio = $F('pc54_datainicio');
   const dataFim = $F('pc54_datatermino');

   if (!dataInicio) {
      return alert('Informe a data de início da vigencia.');
   }

   if (!dataFim) {
      return alert('Informe a data de termino da vigencia.');
   }

   if (dataInicioPosteriorFinal(dataInicio, dataFim)) {
      return alert('Data inicial não pode ser posterior à final.');
   }

   js_divCarregando("Aguarde, salvando abertura Registro de Preço.","msgBox");
   var oParam  = new Object();
   oParam.exec = "salvarAbertura";
   oParam.datainicio  = dataInicio;
   oParam.datatermino = dataFim;
   oParam.liberado    = $('pc54_liberado').checked;
   oParam.resumo      = encodeURIComponent(tagString($F('pc10_resumo')));
   var oAjax          = new Ajax.Request(sUrlRC,
                                         {
                                          method: "post",
                                          parameters:'json='+Object.toJSON(oParam),
                                          onComplete: js_retornoSalvarabertura
                                         });
}

function js_retornoSalvarabertura(oAjax) {

  js_removeObj("msgBox");
  var oRetorno = JSON.parse(oAjax.responseText);
  if (oRetorno.status == 1) {

     parent.iframe_itens.location.href='com4_solicitaaberturaitens.php';
     iframeItens.preparado = true;
     $('pc10_numero').value = oRetorno.iCodigoSolicita;

     var resumo = oRetorno.resumo.urlDecode();

     $('pc10_resumo').value = resumo.replace(/&#(\d+);/g, function (m, n) { return String.fromCharCode(n); });

     parent.mo_camada('itens');

     js_completaPesquisa(oRetorno.iCodigoSolicita);
  } else {
   alert(oRetorno.message.urlDecode());
  }
}

function js_pesquisar() {

  js_OpenJanelaIframe('',
                      'db_iframe_registropreco',
                      'func_solicitaregistropreco.php?funcao_js=parent.js_completaPesquisa|pc54_solicita'+
                      '&anuladas=1&estimativas=1&departamento=true',
                      'Abertura de Registro de Preço',
                      true,
                      0);
}

function js_completaPesquisa(iSolicitacao) {

   var oParam          = new Object();
   oParam.exec         = "pesquisarAbertura";
   oParam.iSolicitacao = iSolicitacao;
   oParam.tipo         = 3;
   db_iframe_registropreco.hide();
   var oAjax           = new Ajax.Request(sUrlRC,
                                         {
                                          method: "post",
                                          parameters:'json='+Object.toJSON(oParam),
                                          onComplete: js_retornoCompletaPesquisa
                                         });
}
function js_retornoCompletaPesquisa(oAjax) {

  var oRetorno = JSON.parse(oAjax.responseText);

  if (oRetorno.status == 1) {

    $('pc54_datainicio').value  = oRetorno.datainicio;
    $('pc54_datatermino').value = oRetorno.datatermino;

    //var resumo = decodeURIComponent(oRetorno.resumo.replace(/\+/g, " "));
    var resumo = oRetorno.resumo.urlDecode();

    $('pc10_resumo').value = resumo.replace(/&#(\d+);/g, function (m, n) { return String.fromCharCode(n); });

    $('pc54_liberado').checked  = oRetorno.liberado;
    $('pc10_numero').value      = oRetorno.solicitacao;

    iframeItens.preparado = true;
    parent.iframe_itens.js_preencheGrid(oRetorno.itens, oRetorno.tipoSolicitacao);

  } else {
    alert(oRetorno.message.urlDecode());
  }

}
function js_imprimir() {

  if ($F('pc10_numero') == "") {

    alert('Abertura nao está salva.\nPara Emitir, salve a abertura.');
    return false;
  }
  var query  = " ini="+$F('pc10_numero');
  query     += "&fim="+$F('pc10_numero');
  query     += "&departamento=<?=db_getsession("DB_coddepto")?>";
   jan = window.open('com2_aberturaregistro002.php?'+query,'',
                     'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
}
$('btnSalvar').observe("click", js_salvarAbertura);
$('btnImprimir').observe("click", js_imprimir);
<?php
if ($lBtnShowBtnConsulta) {
  echo "js_pesquisar();\n";
  echo "\$('btnConsultar').observe('click', js_pesquisar);\n";
  echo "parent.iframe_itens.location.href='com4_solicitaaberturaitens.php'";

}
?>
</script>
