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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <?php
     db_app::load('scripts.js, prototype.js, strings.js, datagrid.widget.js, DBHint.widget.js');
     db_app::load('estilos.css, grid.style.css');
    ?>
  </head>
  <body style='background-color: #cccccc'>
    <div>
      <fieldset>
         <legend>
           <b>Movimentações do Processo</b>
         </legend>
          <div id='ctnDataGridMovimentacoes' style="width: 100%;"></div>
      </fieldset>
    </div>
  </body>
</html>
<div id='ajudaItem' style='position:absolute;border:1px solid #FFDD00; display:none; text-indent: 15px;
                           background-color: #FFFFCC;width: 70%; '>

</div>
<style>
    .sortLink, .sortLink:visited {
        text-decoration: none;
        color: black;
    }
</style>
<script>
var iCodigoProcesso = '<?php echo $oGet->codigo_processo;?>';
var iTipoProcesso = '<?php echo $oGet->tipo_processo;?>';
var sRPC = 'prot4_processoprotocolo004.RPC.php';

var oDataGridMovimentacoes = new DBGrid('gridMovimentacoes');
oDataGridMovimentacoes.nameInstance = 'oDataGridMovimentacoes';
oDataGridMovimentacoes.allowSelectColumns(true);
oDataGridMovimentacoes.setCellWidth(['5%', '5%', '20%', '15%', '20%', '10%', '10%', '7%', '7%']);
oDataGridMovimentacoes.setCellAlign(['center', 'center', 'left', 'left', 'left', 'left', 'left', 'center', 'center' ]);

const url = montarParametroOrdemUrl();
const headers = [
    `<i class="fas fa-arrow-down fa-sm" id="sortIcon"></i><a class="sortLink" href="${url}"> Data </a>`,
    'Hora',
    'Departamento',
    'Instituicao',
    'Usuário',
    'Ocorrência',
    'Despacho',
    'Imprimir',
    'Documentos'
];

oDataGridMovimentacoes.setHeader(headers);
oDataGridMovimentacoes.setHeight(250);
oDataGridMovimentacoes.show($('ctnDataGridMovimentacoes'));
oDataGridMovimentacoes.clearAll(true);

const urlParams = new URLSearchParams(window.location.search);
const sortIcon = document.getElementById('sortIcon');
const direcaoAtual = urlParams.get('ordem') ? urlParams.get('ordem').toUpperCase() : '';

if (direcaoAtual === 'DESC') {
    sortIcon.classList.remove('fa-arrow-down');
    sortIcon.classList.add('fa-arrow-up');
} else if (direcaoAtual === 'ASC') {
    sortIcon.classList.remove('fa-arrow-up');
    sortIcon.classList.add('fa-arrow-down');
}

js_buscarMovimentacoes();

function montarParametroOrdemUrl() {
    const url = new URL(window.location.href);
    const ordemAtual = url.searchParams.get('ordem');
    url.searchParams.delete('ordem');

    if (ordemAtual && ordemAtual.toUpperCase() === 'DESC') {
        url.searchParams.append('ordem', 'ASC');
    } else {
        url.searchParams.append('ordem', 'DESC');
    }

    return url.toString();
}

function js_buscarMovimentacoes() {
    js_divCarregando('Buscando movimentações do processo.', 'msgbox');
    const urlParams = new URLSearchParams(window.location.search);
    const oParametro = {};

    if (urlParams.get('ordem')) {
        oParametro.ordem = urlParams.get('ordem');
    }

    oParametro.exec = 'getMovimentacoesProcesso';
    oParametro.iCodigoProcesso = iCodigoProcesso;

    new Ajax.Request(sRPC, {
        method: 'post',
        parameters: 'json=' + Object.toJSON(oParametro),
        onComplete: function (oAjax) {
            js_removeObj("msgbox");
            const oRetorno = JSON.parse(oAjax.responseText);

            if (oRetorno.lErro) {
                return alert(oRetorno.sMensagem.urlDecode());
            }

            oRetorno.aMovimentacoes.each(function (oMovimento, iSeq) {
                var aLinha = new Array();
                aLinha[0] = oMovimento.sData.urlDecode();
                aLinha[1] = oMovimento.sHora.urlDecode();
                aLinha[2] = oMovimento.iDepartamento + ' - ' + oMovimento.sDepartamento.urlDecode();
                aLinha[3] = oMovimento.iInstituicao + ' - ' + oMovimento.sInstituicao.urlDecode();
                aLinha[4] = oMovimento.sLogin.urlDecode();
                aLinha[5] = oMovimento.sObservacoes.urlDecode();

                var despacho = oMovimento.sDespacho.replace(/%0A/g, "<br>")
                aLinha[6] = despacho.urlDecode();
                // Alteracao Plugin TaxonomiaDeProcessosDoMinisterioPublico - pro3_listamovimentacoesprocesso.php #1
                aLinha[7] = '';
                aLinha[8] = '';

                if (oMovimento.lImprimir) {

                    var sButton = "<input type='button' value='imprimir' name='Imprimir' ";
                    sButton += " onclick='js_imprimeDespacho(\"" + iCodigoProcesso + "\",\"" + oMovimento.iAndamentoInterno + "\")' />";
                    aLinha[7] = sButton;
                }

                if (oMovimento.lAnexos) {
                    var sButton = "<input type='button' value='Anexos' name='Anexos' ";
                    sButton += " onclick='js_abrepopup(\"" + iCodigoProcesso + "\",\"" + oMovimento.iAndamentoInterno + "\")' />";

                    if (iTipoProcesso == 2) {
                        var sButton = "<input type='button' value='Visualizar' name='Visualizar' ";
                        sButton += " onclick='visualizarDocumentosDespacho(\"" + iCodigoProcesso + "\",\"" + oMovimento.iAndamentoInterno + "\")' />";
                    }

                    aLinha[8] = sButton;
                }

                oDataGridMovimentacoes.addRow(aLinha);

                for (var iHint = 0; iHint <= 6; iHint++) {

                    var sCampo = iHint != 6 ? aLinha[iHint] : oMovimento.sDespacho;
                    if (iHint == 2) {
                        sCampo += ' / ' + oMovimento.sOrgao.urlDecode();
                    }
                    oDataGridMovimentacoes.aRows[iSeq].aCells[iHint].sEvents += " onmouseover='js_displayAjuda(\"" + sCampo + "\", true)'";
                    oDataGridMovimentacoes.aRows[iSeq].aCells[iHint].sEvents += " onmouseout='js_displayAjuda(\"\", false)'";
                }
            });
            oDataGridMovimentacoes.renderRows();
        }
    });

}

function js_displayAjuda(sTexto, lShow) {

  if (lShow) {

    el     =  $('ctnDataGridMovimentacoes');
    var oElemento = $('ajudaItem');
    var x  = 0;
    var y  = el.offsetHeight;
        x += el.offsetLeft;
        y += el.offsetTop;

    var despacho = sTexto.replace(/%0A/g, "<br>")
    oElemento.innerHTML     = despacho.urlDecode()
    oElemento.style.display = '';
    oElemento.style.top     = '10px';
    oElemento.style.left    = x;
    oElemento.style.borderRadius = '2px';
    oElemento.style.padding = '4px';

  } else {
    $('ajudaItem').style.display = 'none';
  }
}

function js_imprimeDespacho(codproc, codprocandamint) {

  var sUrl = 'pro2_despachointer002.php?codproc='+codproc+'&codprocandamint='+codprocandamint;
  jan = window.open(sUrl, '', 'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}

function js_abrepopup(codproc, codprocandamint) {
  js_OpenJanelaIframe('CurrentWindow.corpo',
                      'db_iframe_documentosprocesso',
                      'func_listaanexosdespacho.php?codproc='+codproc+'&codprocandamint='+codprocandamint,
                      'Lista de Documentos',
                      true);
}

visualizarDocumentosDespacho = (codigoProcesso, procandamint) => {

  getEcidadeInfo().then(apiUrl => {
    const data = new FormData();
    data.append('codigoProcesso', codigoProcesso);

    HttpClient.post(`${apiUrl}patrimonial/protocolo/processo/processodocumento/documentosPorProcesso`, {body: data}).then(response => {
        if(response.error == true){
          alert(response.message);
            return;
        }

        var codigosEStorage = [];
        var index = 0;

        if(!!procandamint){
          var ordem = response.data.filter(
            (dados) => dados.codigo_andamento_interno == procandamint
          ).map((dados) => dados.ordem).reduce(
            (accumulator, currentValue) => (currentValue < accumulator) ? currentValue : accumulator
          );

          index = (ordem || 1) - 1;
        }

        response.data.forEach((documento) => {
          codigosEStorage.push(documento.id_estorage);
        });


        if (codigosEStorage.length == 0) {
          alert("Nenhum documento encontrado para o processo.");
          return false;
        }

    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_visualizador_imagens', `db_visualizador_documentos.php?ids=${codigosEStorage}&viewIndex=${index}`, 'Visualizador de documentos', true);
    });
  });
}


function getEcidadeInfo() {

    const data = new FormData();
    data.append('acao', 'info');
    return HttpClient.post('con4_ecidadeinfo.RPC.php', { body: data }).then(function (response) {
        if (response.erro) {
            alert(response.mensagem);
            return;
        }

        return response.url + 'v4/api/';
    });

}
</script>
