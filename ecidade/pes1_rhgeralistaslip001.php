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

$oGet = db_utils::postMemory($_GET);

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
     <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>

    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container" style="width: 1000px">
    <form name="form1" method="post" action="">
        <fieldset>
            <legend>
                <b>Geração de SLIP</b>
            </legend>

            <div id='listaSLIP2'></div>

        </fieldset>
        <input name="fechar" id="fechar " type="button" value="Fechar" onClick="js_fechar();">
        <input name="processar" id="processar" type="button" value="Processar" onClick="js_processar();"
               disabled>
    </form>
</div>
</body>
</html>
<script>

    const collection = new Collection().setId('uniquiId');
    var gridSplips = new DatagridCollection(collection).configure({
        order: false,
        height: 300
    });

    gridSplips.addColumn('fonte_recurso', {label: "Recurso", width: '10%', align: 'center'}).transform((item, linha) => {
        return `${linha.fonte_recurso} - ${linha.subrecurso}`;
    })
    gridSplips.addColumn('descricao_recurso', {label: "Recurso", width: '20%', align: 'left'}).transform('decode');
    gridSplips.addColumn('complemento', {label: "Complemento", width: '13%', align: 'left'}).transform('decode');
    gridSplips.addColumn('conta_debito', {label: "Conta Débito", width: '22%', align: 'center'})
    .transform((item, linha) => {
        return  js_getSelect('contaDeb' + linha.recurso + linha.retencao + linha.folhaslip, linha.conta_debito);
    });
    gridSplips.addColumn('conta_credito', {label: "Conta Crédito", width: '22%', align: 'center'})
    .transform((item, linha) => {
        return  js_getSelect('contaCred' + linha.recurso + linha.retencao + linha.folhaslip, linha.conta_credito);
    });
    gridSplips.addColumn('valor', {label: "Valor", width: '13%', align: 'right'}).transform('dinheiro');

    gridSplips.getGrid().hasTotalizador = true;
    gridSplips.show($('listaSLIP2'));

    gridSplips.setEvent('onafterrenderrows', function(dados) {

        dados.itens.forEach((linha) => {
            $('contaDeb' + linha.recurso + linha.retencao + linha.folhaslip).value = linha.conta_debito;
            $('contaCred' + linha.recurso + linha.retencao + linha.folhaslip).value = linha.conta_credito;
        });
    });

    var sUrl = 'pes1_rhempenhofolhaRPC.php';
    var lSelect = false;

    function js_processar() {
        js_divCarregando('Aguarde...', 'msgBox');

        let aSlips = collection.get().map(slip => {
            slip = slip.build();
            slip.complemento = encodeURIComponent(tagString(slip.complemento));
            slip.descricao_recurso = encodeURIComponent(tagString(slip.descricao_recurso));
            slip.conta_debito =$F('contaDeb' + slip.recurso + slip.retencao + slip.folhaslip);
            slip.conta_credito =$F('contaCred' + slip.recurso + slip.retencao + slip.folhaslip);
            return slip;
        });

        let sQuery = 'sMethod=geraSLIP';
        sQuery += '&iAnoFolha=' + iAnoFolha;
        sQuery += '&iMesFolha=' + iMesFolha;
        sQuery += '&sSigla=' + sSigla;
        sQuery += '&sSemestre=' + sSemestre;
        sQuery += '&aSlips=' + JSON.stringify(aSlips);

        new Ajax.Request(sUrl, {
                method: 'post',
                parameters: sQuery,
                onComplete: js_retornoProcessaSLIP
            }
        );
    }

    function js_retornoProcessaSLIP(oAjax) {
        js_removeObj("msgBox");

        var aRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');
        var sMensagem = aRetorno.sMsg.urlDecode().replace(sExpReg, '\n');

        if (aRetorno.lErro) {
            alert(sMensagem);
            return false;
        } else {

            if (confirm(sMensagem + "\nDeseja Imprimir?")) {
                window.open('cai3_emiteslips002.php?slips=' + aRetorno.sListaSlips, '', 'location=0');
            }

            js_fechar();
        }
    }


    function js_fechar() {
        parent.db_iframe_geraslip.hide();
    }

    function js_verificaSelect() {

        if (lSelect) {
            clearTimeout(temporizador);
            js_consultaSLIPs();
        } else {
            temporizador = setTimeout('js_verificaSelect()', 500);
        }
    }

    function js_consultaSLIPs() {

        if (!lSelect) {
            js_consultaSelectContas();
            js_verificaSelect();
        } else {
            js_divCarregando('Aguarde...', 'msgBox');

            var sQuery = 'sMethod=consultarDadosGeracaoSLIP';
            sQuery += '&iAnoFolha=' + iAnoFolha;
            sQuery += '&iMesFolha=' + iMesFolha;
            sQuery += '&sSigla=' + sSigla;
            sQuery += '&sSemestre=' + sSemestre;
            if (sSigla == 'r20') {
                sQuery += '&sRescisoes=' + sRescisoes;
            }

            new Ajax.Request(sUrl, {
                    method: 'post',
                    parameters: sQuery,
                    onComplete: js_retornoConsultaSLIP
                }
            );
        }
    }


    function js_retornoConsultaSLIP(oAjax) {

        js_removeObj("msgBox");

        var aRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');

        if (aRetorno.lErro) {
            alert(aRetorno.sMsg.urlDecode().replace(sExpReg, '\n'));
            js_fechar();
            return false;
        } else {
            if (aRetorno.lLiberada) {
                $('processar').disabled = false;
            }
            js_montaGrid(aRetorno.aSlips);
        }
    }

    function js_montaGrid(aSlips) {
        var nTotal = Number();

        gridSplips.clear();
        aSlips.forEach((slip, seq) => {
            if (slip.conta_credito == '') {
                alert(`Recurso ${slip.recurso} - ${slip.descricao_recurso.urlDecode()} sem conta crédito configurada! Verifique.`);
                js_fechar();
                return false;
            }

            slip.uniquiId = seq
            collection.add(slip);
            nTotal += Number(slip.valor);
        });

        gridSplips.reload();

        $('TotalForCol5').innerHTML = js_formatar(nTotal, 'f');
    }

    function js_consultaSelectContas() {

        js_divCarregando('Aguarde...', 'msgBox');

        var sQuery = 'sMethod=consultarSelectContas';
        new Ajax.Request(sUrl, {
                method: 'post',
                parameters: sQuery,
                onComplete: js_retornoConsultaSelectContas
            }
        );
    }

    function js_retornoConsultaSelectContas(oAjax) {

        js_removeObj("msgBox");

        var aRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');

        if (aRetorno.lErro) {
            alert(aRetorno.sMsg.urlDecode().replace(sExpReg, '\n'));
            return false;
        } else {

            var iNroLinhas = aRetorno.aContas.length;
            sOpcoes = '';

            if (iNroLinhas > 0) {
                for (var iInd = 0; iInd < iNroLinhas; iInd++) {
                    with (aRetorno.aContas[iInd]) {
                        sOpcoes += "<option value='" + reduz + "'>" + descr.urlDecode() + "</option>";
                    }
                }
            }

            lSelect = true;
        }
    }

    function js_getSelect(sNome, sDefault) {
        var sSelect = "<select name='" + sNome + "' id='" + sNome + "' style='width:100%'>";
        sSelect += sOpcoes;
        sSelect += "</select>";

        return sSelect;
    }

</script>
<?php

if (trim($oGet->iAnoFolha) != '' && trim($oGet->iMesFolha) != '' && trim($oGet->sSigla) != '') {


    if (isset($oGet->sSemestre)) {
        $sSemestre = $oGet->sSemestre;
    } else {
        $sSemestre = '';
    }
    echo "<script>
   	       var iAnoFolha  =  {$oGet->iAnoFolha};
   	       var iMesFolha  =  {$oGet->iMesFolha};
   	       var sSigla     = '{$oGet->sSigla}';
   	       var sSemestre  = '{$sSemestre}';
   	       var sRescisoes = '" . @$oGet->sRescisoes . "';
   	     </script>";

    echo "<script>js_consultaSLIPs();</script>";
}
?>
