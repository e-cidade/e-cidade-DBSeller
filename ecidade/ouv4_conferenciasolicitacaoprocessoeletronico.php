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
$tipoProcesso = $oGet->tipoProcesso;
$processo = $oGet->processo;
$ano = $oGet->ano;
$escondeBotoes = $oGet->escondeBotoes;
$codigoProcessoProtocolo = isset($oGet->codigoProcessoProtocolo) ? $oGet->codigoProcessoProtocolo : null;
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/classes/patrimonio/ouvidoria/processoeletronico/AprovacaoRejeicao.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <style>
        .button-container {
            margin-top: 10px;
            text-align: center;
        }

        #containerSolicitacaoAlvara {
            width: 800px;
        }

        .table_requerente > tr > td:nth-child(odd) {
            width: 12.5%;
        }

        .table_requerente > tr > td:nth-child(even) {
            width: 37.5%;
        }

        td:nth-child(odd) {
            width: 20%;
        }

        td:nth-child(even) {
            width: 75%;
        }

        td > input {
            width: 95%;
        }

        .table_documentos > tr > td:nth-child(1) {
            width: 60%;
        }

        .table_documentos > tr > td:nth-child(1) > input {
            width: 98%;
        }

        .table_documentos > tr > td:nth-child(2) {
            width: 40%;
        }

        .table_documentos > tr > td:nth-child(2) > input {
            width: 25%;
        }

    </style>
</head>
<body bgcolor="#CCCCCC" onLoad="a=1">
<div class="container">
    <form id="form1" name="form1">
        <?php db_input('tipoProcesso', 10, 0, false, 'hidden', 3, ""); ?>
        <?php db_input('processo', 10, 0, false, 'hidden', 3, ""); ?>
        <?php db_input('ano', 10, 0, false, 'hidden', 3, ""); ?>
        <?php db_input('codigoProcessoProtocolo', 10, 0, false, 'hidden', 3, ""); ?>
        <fieldset id="containerSolicitacaoAlvara" name="containerSolicitacaoAlvara">
            <legend id="CotaMunicipio"><b>Solicitação de Álvará</b></legend>
        </fieldset>
        <div class='button-container' <?= ($escondeBotoes == 'true') ? 'style="display:none;"' : ''?>>
            <input type="button" name="btnAprovar" id="btnAprovar" value="Aprovar"/>
            <input type="button" name="btnRejeitar" id="btnRejeitar" value="Rejeitar"/>
        </div>
    </form>
</div>
<script>
    const
        urlRpc                  = 'ouv4_solicitacaoprocessoeletronico.RPC.php',
        elemento                = $('containerSolicitacaoAlvara'),
        tipoProcesso            = $('tipoProcesso'),
        processo                = $('processo'),
        ano                     = $('ano'),
        codigoProcessoProtocolo = $('codigoProcessoProtocolo'),
        classificacaoGrau       = {
            'B' : 1,
            'M' : 2,
            'A' : 3
        };

    getDadosProcesso().then(response => {
        this.atividades = response.atividades || response.empresa.atividades;
        montaDadosFormulario(response, elemento);
    });

    function montaDadosFormulario(objDadosAlvara, element) {
        var
            count = 0,
            tr = document.createElement('tr');

        for (parametro in objDadosAlvara) {

            if (!!objDadosAlvara[parametro] && typeof objDadosAlvara[parametro] == "object" && typeof objDadosAlvara[parametro].value != 'undefined') {
                if (element.nodeName == 'TABLE') {
                    var
                        tdLabel = document.createElement('td'),
                        label = document.createElement('label'),
                        td = document.createElement('td'),
                        input = document.createElement('input');

                    if (!(element.classList.contains('table_requerente')) || count == 2) {
                        tr = document.createElement('tr');
                        count = 0;
                    }

                    label.appendChild(document.createTextNode(`${objDadosAlvara[parametro].label}:`));
                    tdLabel.appendChild(label);

                    input.readonly = true;
                    input.disabled = true;
                    input.type = 'text';
                    input.id = parametro;
                    input.name = parametro;

                    const node = objDadosAlvara[parametro].value;

                    if (node !== null) {
                        if (typeof node === 'object') {
                            input.value = `${node.codigo} ${(!!node.descricao) ? ' - ' + node.descricao : ''}`;
                        } else {
                            input.value = formataInformacao(node);
                        }
                    }

                    td.appendChild(input);

                    if (element.classList.contains('table_documentos')) {
                        var
                            inputDownload = document.createElement('input');

                        tdLabel = td;
                        input.value = objDadosAlvara[parametro].label;
                        td = document.createElement('td');

                        inputDownload.id = `download_${objDadosAlvara[parametro].value}`;
                        inputDownload.type = 'button';
                        inputDownload.value = `Download`;
                        inputDownload.setAttribute('idArquivo', objDadosAlvara[parametro].value);
                        inputDownload.classList.add(`btnDownload`);
                        inputDownload.addEventListener('click', event => {

                            let elemento = event.target;
                            let formData = new FormData();
                            formData.append('exec', 'downloadArquivo');
                            formData.append('id', elemento.getAttribute('idArquivo'));

                            HttpClient.post(urlRpc, {body: formData}).then(response => {
                                if (response.erro) {
                                    return alert(response.mensagem);
                                }

                                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_download', 'db_download.php?arquivo=' + response.fileName, 'Download de arquivos', false);
                            });
                        });

                        td.appendChild(inputDownload);

                    }

                    tr.appendChild(tdLabel);
                    tr.appendChild(td);

                    element.appendChild(tr);

                    count++;
                }
            } else if (!!objDadosAlvara[parametro] && typeof objDadosAlvara[parametro] == "object") {
                var
                    fieldset = document.createElement('fieldset'),
                    legend = document.createElement('legend'),
                    table = document.createElement('table');

                if (objDadosAlvara[parametro].label) {
                    legend.appendChild(document.createTextNode(objDadosAlvara[parametro].label));
                } else {
                    legend.appendChild(document.createTextNode(parametro.split("_").map((element) => {
                        return element.charAt(0).toUpperCase() + element.slice(1)
                    }).join(" ")));
                }

                fieldset.appendChild(legend);
                fieldset.appendChild(table);
                fieldset.classList.add(`fieldset_${parametro}`);
                table.classList.add('form-container', `table_${parametro}`);

                if (element.nodeName == 'TABLE') {
                    element.parentElement.appendChild(fieldset);
                } else {
                    element.appendChild(fieldset);
                }

                montaDadosFormulario(objDadosAlvara[parametro], table);
            }
        }

        return element;
    }

    function getMaiorGrauRisco()
    {
        risco = this.atividades.atividade.risco;

        for(var key in this.atividades.atividades_secundarias || {}){
            var atividade = this.atividades.atividades_secundarias[key];
            if(classificacaoGrau[atividade.atividade.risco] > classificacaoGrau[risco]){
                risco = atividade.atividade.risco;
            }
        }

        return risco;
    }

    function getDadosProcesso() {
        var
            oParametros = {
                'exec': 'solicitacaoProcessoAlvara',
                'numeroProcesso': processo.value,
                'anoProcesso': ano.value,
                'tipoProcesso': tipoProcesso.value,
                'codigoProcessoProtocolo' : codigoProcessoProtocolo.value
            },
            formData = createFormData(oParametros);

        return HttpClient.post(urlRpc, {body: formData}).then(response => {

            if (response.erro) {
                return alert(response.mensagem);
            }
            return JSON.parse(response.solicitacaoProcessoAlvara);
        });
    }

    function createFormData(oParametros) {
        var formData = new FormData();
        for (parametro in oParametros) {
            if (oParametros[parametro] instanceof Array) {
                formData.append(`${parametro}[]`, oParametros[parametro]);
            } else {
                formData.append(parametro, oParametros[parametro]);
            }
        }
        return formData;
    }

    function formataInformacao(informacao)
    {
        var valor = informacao;

        const regex = /^\d{4}-(0[1-9]|1[0,1,2])-(0[1-9]|[1,2][0-9]|3[0,1])$/;
        let m;
        if ((m = regex.exec(valor)) !== null) {
            valor = js_formatar(valor, 'd');
        }

        return valor;

    }

    $('btnAprovar').addEventListener('click', event => {
        try {
            let
                grauRisco = getMaiorGrauRisco(),
                aprovacao = new AprovacaoRejeicao(1, processo.value, ano.value, tipoProcesso.value, grauRisco);

            aprovacao.show();
        } catch (exception) {
            alert(exception);
        }
    });

    $('btnRejeitar').addEventListener('click', event => {
        try {
            let aprovacao = new AprovacaoRejeicao(2, processo.value, ano.value, tipoProcesso.value);

            aprovacao.show();
        } catch (exception) {
            alert(exception);
        }
    })
</script>
</body>
</html>
