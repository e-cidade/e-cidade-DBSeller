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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

$aSituacaoPortarias = array(
    'C' => 'Criada',
    'O' => 'Conferido',
    'D' => 'Devolvido para abertura',
    'A' => 'Aguarda assinatura',
    'F' => 'Devolvido para conferência',
    'S' => 'Assinado',
    'I' => 'Impresso',
);

$anousu = db_getsession("DB_anousu");

if (!isset($dbcadastro)) {
    $dbcadastro = false;
}

?>

<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>

</head>

<body class='body-default'>
    <div class='container' style='width:400px;'>
        <form action="post" name='form1'>
            <fieldset>
                <legend>Parâmetros de pesquisa</legend>
                <table class='form-container'>
                    <tr>
                        <td title="Tipo Portaria">
                            <?php db_ancora('Tipo Portaria:', "js_pesquisa_h31_portariatipo(true)", ""); ?>
                        </td>
                        <td>
                            <?php
                            db_input('h31_portariatipo', 10, null, true, 'text', "", " style='width:120px;' onchange='js_pesquisa_h31_portariatipo(false)';");
                            db_input("h12_descr", 40, null, true, "text", 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Ano:</b>
                        </td>
                        <td>
                            <?php db_input("anousu", 15, null, true, "text", 1, ""); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                <?php db_ancora("Portaria de:", "js_pesquisaPortariaIncial();", 1, ""); ?>
                            </b>
                        </td>
                        <td>
                            <?php db_input("porti", 15, null, true, "text", 1, ""); ?>

                            <b><?php db_ancora("Até:", "js_pesquisaPortariaFinal();", 1, ""); ?></b>

                            <?php db_input("portf", 15, null, true, "text", 1, ""); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Situação:</b>
                        </td>
                        <td>
                            <?php db_select('situacaoPortarias', $aSituacaoPortarias, $dbcadastro, 1) ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" value="Pesquisar" id='btnPesquisar' />
        </form>

        <div id="certificatesDIV" style="display: none;">
            <fieldset>
                <legend>Certificados</legend>
                <div class="mb-3">
                    <select class="form-select" id="certificateSelect" aria-label="Default select example">
                        <option value="asdas">asdads</option>
                    </select>
                </div>
            </fieldset>
        </div>

    </div>

    <div class='subcontainer' style='width:1280px;'>
        <fieldset>
            <legend>Portarias</legend>
            <div id='ctnGrid'></div>
        </fieldset>
        <input type="button" value="Assinar A3" id='btnProcessarAssinaturaA3' disabled="true" />
        <!-- <input type="button" value="Assinar" id='btnProcessarAssinatura' disabled="true" /> -->
        <input type="button" value="Alterar Situação" id='btnAlterarSituacao' />
    </div>

    <?php db_menu(); ?>
    <script type="text/javascript" src="scripts/session.js"></script>
    <!-- <script language="JavaScript" type="text/javascript" src="scripts/pades-signer-sdk.min.js"></script> -->
    <script type="text/javascript" src="scripts/classes/rh/DBViewAssinaturaPortaria.js"></script>
    <script type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
    <script type="text/javascript" src="scripts/socket.io.js"></script>
    <script>
        /*
         * Inicio Assinador A3
         */

        // Desbloqueio do botão de assinar
        var unlockSignButton = false;
        var certificateAlias = 0;

        var filesToSign = 0;
        var counterFilesSigned = 0;

        var arquivosAssinar = [];

        //Instanciar socket.io
        var socket = io.connect('http://localhost:9000');

        socket.on('connect', () => {
            alert("Conectado ao Assinador A3");
        });

        document.querySelector('#certificateSelect').addEventListener('change', (event) => {

            certificateAlias = event.target.value;

        });

        // Caso troque a situação de portaria
        document.querySelector('#situacaoPortarias').addEventListener('change', (event) => {

            if (event.target.value == "A") {
                unlockSignButton = true;
            } else {
                unlockSignButton = false;
            }

        });

        // Botão de pesquisar pressionado
        document.querySelector('#btnPesquisar').addEventListener('click', (event) => {

            if (unlockSignButton) {
                socket.emit('getCertificates', {});
                js_divCarregando('Obtendo certificados', 'loading_message_a3');
            }

        });

        //Recebe resposta dos certificados e propaga o select
        socket.on('certificates', (event) => {
            let certificates = event.certList;
            let selectBox = document.querySelector('#certificateSelect');

            while (selectBox.options.length > 0) {
                selectBox.remove(0);
            }

            document.querySelector('#btnProcessarAssinaturaA3').disabled = !unlockSignButton;
            if (unlockSignButton) {
                document.querySelector('#certificatesDIV').style.display = "block";
            } else {
                document.querySelector('#certificatesDIV').style.display = "none";
            }

            selectBox.add(new Option('Selecione um certificado', 0));

            certificates.forEach((value, index, arr) => {
                let newOption = new Option(value, value);
                selectBox.add(newOption);
            });

            js_removeObj('loading_message_a3');

        });

        // Botão de assinar pressionado
        document.querySelector('#btnProcessarAssinaturaA3').addEventListener('click', (event) => {

            js_divCarregando('Assinando documentos', 'loading_message_a3');

            const portariasSelecionadas = DBViewAssinaturaPortaria.oGridPortarias.grid.getSelection('object');

            if( certificateAlias == 0 ){
                alert('Precisa selecionar um certificado!')
                return;
            }

            console.log(portariasSelecionadas[0].itemCollection);

            arquivosAssinar = [];
            portariasSelecionadas.forEach((value, index, arr) => {

                const item = value.itemCollection;

                arquivosAssinar.push(item);

                let papel = item?.assinante?.papel;
                let nomeAssinar = item?.assinante?.nome_assinar;
                const tmpArray = {
                    fileID: item.iIdestorage,
                    fileB64: "",
                    fileCertificate: certificateAlias,
                    userRole: papel,
                    userMatric: "",
                    isCertBase64: false,
                    nameSigner: nomeAssinar,
                    sPortaria: item.sPortaria,
                    sAno: item.sAno,
                    sId: item.sId
                };

                PHPSession.loadData().then(() => {

                    HttpClient.get(`${PHPSession.requestApi}/assinador/obter-arquivo-base64/${item.iIdestorage}`,{
                        reportProgress: false
                    })
                        .then(response => {
                            let data = response['data'];
                            tmpArray.fileB64 = data.content_estorage;

                            filesToSign = filesToSign + 1;
                            socket.emit('sign', tmpArray);

                        });

                });

            });

        });

        socket.on('signed', (event) => {

            counterFilesSigned = counterFilesSigned + 1;

            let fileB64 = event.fileB64;

            arquivoAssinado = arquivosAssinar.filter(file => {
                return event.fileID == file.iIdestorage
            }).shift();

            console.log("@@", arquivoAssinado);

            PHPSession.loadData().then(() => {

                const param = {
                    isB64: true,
                    b64file: fileB64,
                    assinante: event.fileCertificate,
                    oldIdEstorage: event.fileID,
                    portaria: {
                        sPortaria: event.sPortaria,
                        sAno: event.sAno,
                        sId: event.sId,
                    },
                    files: [],
                    signers_need: []
                };

                DBViewAssinaturaPortaria.oCollectionPortarias.remove(param.portaria.sId);
                DBViewAssinaturaPortaria.oGridPortarias.reload();

                console.log("==>", param);


                const formData = new FormData();
                formData.append('id', arquivoAssinado.ID);
                PHPSession.appendFormData(formData);

                HttpClient.post(`${PHPSession.requestApi}/assinador/obter-assinantes-portaria`, {
                        body: formData,
                        reportProgress: false
                    })
                    .then(response => {

                        response.forEach((val, ind, arr) => {
                            param.signers_need.push(val);
                        });

                        const parametros = new FormData();
                        parametros.append('exec', 'salvarDocumentoAssinado');
                        parametros.append('json', JSON.stringify(param));

                        fetch(DBViewAssinaturaPortaria.requestRPC, {
                            method: 'POST',
                            body: parametros,
                            credentials: 'include',
                        }).then(function(resp) {
                            console.log(resp);

                            console.log("IF COUNTER", counterFilesSigned >= filesToSign);

                            if( counterFilesSigned >= filesToSign ) {
                                js_removeObj('loading_message_a3');
                                alert('Todos documentos foram assinados com sucesso!');
                            }

                            return resp.json();

                        });



                    });


            });

        });

        socket.on('exception', (event) => {
            alert(event);
            filesToSign = 0;
            counterFilesSigned = 0;
            js_removeObj('loading_message');
            js_removeObj('loading_message_a3');
        });

        /*
         * Fim Assinador A3
         */

        (function() {
            document.querySelector('#btnPesquisar').addEventListener('click', (ev) => {
                DBViewAssinaturaPortaria.getPortarias()
                    .then(() => {
                        const selectSituacaoPortaria = document.querySelector('#situacaoPortarias');

                        if (selectSituacaoPortaria.value == 'A') {
                            DBViewAssinaturaPortaria.refreshSignatureStatusFiles();
                        }
                    });
            });

            document.querySelector('#btnAlterarSituacao').addEventListener('click', (ev) => {

                var
                    proximaSituacao = null,
                    situacaoAnterior = null,
                    selectSituacaoPortaria = document.querySelector('#situacaoPortarias');

                switch (selectSituacaoPortaria.value) {

                    case 'C': //Criada
                    case 'D': //Devolvido para abertura
                        proximaSituacao = 'O';
                        break;

                    case 'O': //Conferido
                    case 'F': //Devolvido para conferência
                        proximaSituacao = 'A';
                        situacaoAnterior = 'D';
                        break;

                    case 'A': //Aguarda assinatura
                        situacaoAnterior = 'F';
                        proximaSituacao = 'S';
                        break;

                    case 'S': //Assinado
                        proximaSituacao = 'I';
                        break;

                    case 'I': //Impresso
                        situacaoAnterior = 'S';
                        break;
                }

                windowAlterarSituacaoPortaria = new windowAux('AlterarSituacaoPortaria', 'Alterar Situação da Portaria', 450, 250);
                windowAlterarSituacaoPortaria.zIndex = 2;
                windowAlterarSituacaoPortaria.setContent('');
                windowAlterarSituacaoPortaria.setShutDownFunction(function() {
                    windowAlterarSituacaoPortaria.destroy();
                });

                windowAlterarSituacaoPortaria.show(null, null, true);
                windowAlterarSituacaoPortaria.getContentContainer().load('rh_processaassinaturadigitalalterasituacao.php', function() {

                    var btnProximaSituacao = document.querySelector('#proximaSituacaoIr');
                    var btnSituacaoAnterior = document.querySelector('#situacaoAnteriorIr');
                    var inputNumeroPortaria = document.querySelector('#numeroPortaria');
                    var inputServidor = document.querySelector('#servidor');
                    var inputProximaSituacao = document.querySelector('#proximaSituacao');
                    var inputSituacaoAnterior = document.querySelector('#situacaoAnterior');

                    inputProximaSituacao.value = proximaSituacao != null ? retornarLabelSitucao(proximaSituacao) : '';
                    inputSituacaoAnterior.value = situacaoAnterior != null ? retornarLabelSitucao(situacaoAnterior) : '';

                    inputNumeroPortaria.parentElement.parentElement.remove();
                    inputServidor.parentElement.parentElement.remove();

                    if (proximaSituacao === null) {
                        btnProximaSituacao.setAttribute('disabled', true);
                    } else {
                        btnProximaSituacao.addEventListener('click', function(event) {
                            alterarSituacaoEmLote.call(windowAlterarSituacaoPortaria, proximaSituacao);
                        });
                    }

                    if (situacaoAnterior === null) {
                        btnSituacaoAnterior.setAttribute('disabled', true);
                    } else {
                        btnSituacaoAnterior.addEventListener('click', function(event) {
                            alterarSituacaoEmLote.call(windowAlterarSituacaoPortaria, situacaoAnterior);
                        });
                    }
                });
            });
        })()

        function alterarSituacaoEmLote(situacao) {

            var alteracoesSituacoes = [];
            var portarias = DBViewAssinaturaPortaria.oGridPortarias.grid.getSelection('object');

            js_divCarregando('Alterando situação das portarias', 'loading_message');

            portarias.forEach(item => alteracoesSituacoes.push(DBViewAssinaturaPortaria.alterarSituacao(situacao, item.itemCollection)));

            Promise.all(alteracoesSituacoes)
                .then(responses => {

                    responses.forEach(response => {

                        if (response.erro) {
                            throw response.mensagem;
                        }
                    })

                    js_removeObj('loading_message');

                })
                .catch(err => {
                    js_removeObj('loading_message');
                    alert(err);
                });

            this.destroy();
        }

        function js_pesquisaPortariaIncial() {
            js_OpenJanelaIframe('', 'db_iframe_portariai', 'func_portaria.php?funcao_js=parent.js_mostraportariai|h31_numero', 'Pesquisa', true);
        }

        function js_mostraportariai(chave) {
            document.form1.porti.value = chave;
            db_iframe_portariai.hide();
        }

        function js_pesquisaPortariaFinal() {
            js_OpenJanelaIframe('', 'db_iframe_portariaf', 'func_portaria.php?funcao_js=parent.js_mostraportariaf|h31_numero', 'Pesquisa', true);
        }

        function js_mostraportariaf(chave) {
            document.form1.portf.value = chave;
            db_iframe_portariaf.hide();
        }

        function js_pesquisa_h31_portariatipo(mostra) {

            if (mostra == true) {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_portariatipo',
                    'func_portariatipodescrato.php?funcao_js=parent.js_mostrah31_portariatipo1|h30_sequencial|h12_descr|h30_amparolegal|h41_descr|h12_natureza|h12_codigo', 'Pesquisa', true);
            } else {
                if (document.form1.h31_portariatipo.value != '') {
                    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_portariatipo',
                        'func_portariatipodescrato.php?pesquisa_chave=' + document.form1.h31_portariatipo.value + '&funcao_js=parent.js_mostrah31_portariatipo', 'Pesquisa', false);
                } else {
                    document.form1.h31_portariatipo.value = '';
                    document.form1.h12_descr.value = '';
                }
            }
        }

        function js_mostrah31_portariatipo(chave1, erro, chave2, chave3, chave4, chave5, chave6) {

            if (erro == true) {
                document.form1.h31_portariatipo.value = '';
                document.form1.h12_descr.value = '';

            } else {
                document.form1.h31_portariatipo.value = chave1;
                document.form1.h12_descr.value = chave2;

            }
        }

        function js_mostrah31_portariatipo1(chave1, chave2, chave3, chave4, chave5, chave6) {

            document.form1.h31_portariatipo.value = chave1;
            document.form1.h12_descr.value = chave2;

            db_iframe_portariatipo.hide();

        }
    </script>

</body>

</html>
